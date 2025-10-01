<?php

namespace MauticPlugin\MauticNrsSmsBundle\Transport;

use Mautic\LeadBundle\Entity\Lead;
use Mautic\SmsBundle\Sms\TransportInterface;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\SmsBundle\Entity\Stat;
use Psr\Log\LoggerInterface;

/**
 * The main transport class responsible for sending SMS via the 360NRS API.
 */
class NrsSmsTransport implements TransportInterface
{
    private const API_ENDPOINT = 'https://dashboard.360nrs.com/api/rest/sms';

    private IntegrationHelper $integrationHelper;
    private LoggerInterface $logger;

    public function __construct(IntegrationHelper $integrationHelper, LoggerInterface $logger)
    {
        $this->integrationHelper = $integrationHelper;
        $this->logger            = $logger;
    }

    /**
     * Sends an SMS to a given Lead.
     *
     * @param Lead   $lead      The Mautic Lead (contact) to send the SMS to.
     * @param string $content   The message content of the SMS.
     * @param Stat|null $stat   Stat entity for tracking (optional).
     *
     * @return bool|string Returns true on success, or an error message string on failure.
     */
    public function sendSms(Lead $lead, $content, Stat $stat = null)
    {
        $phone = $lead->getMobile() ?: $lead->getPhone();
        if (empty($phone)) {
            return 'The contact does not have a phone number.';
        }

        try {
            $integration = $this->integrationHelper->getIntegrationObject('NrsSms');
            if (!$integration || !$integration->getIntegrationSettings()->getIsPublished()) {
                return '360NRS SMS integration is not published.';
            }

            $keys = $integration->getDecryptedApiKeys();
            $settings = $integration->getIntegrationSettings()->getFeatureSettings();

            $authToken = $keys['username'] ?? null;
            $fromName = $settings['from_name'] ?? null;

            if (empty($authToken) || empty($fromName)) {
                return 'Authorization Token or From Name is not configured.';
            }

            $payload = json_encode([
                'to'      => [$phone],
                'from'    => $fromName,
                'message' => $content,
            ]);

            $ch = curl_init(self::API_ENDPOINT);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Basic ' . $authToken,
                'Content-Length: ' . strlen($payload)
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                $this->logger->error('360NRS SMS API cURL Error: '.$error);
                return 'API Communication Error: '.$error;
            }

            $this.logger->debug('360NRS SMS API Response (Code '.$httpCode.'): '.$response);
            $responseData = json_decode($response, true);

            if (in_array($httpCode, [202, 207]) && isset($responseData['result'][0]['accepted']) && $responseData['result'][0]['accepted'] === true) {
                return true; // Success!
            }
            
            $errorMessage = $responseData['result'][0]['error']['description'] ?? 'Failed to send SMS with HTTP status code ' . $httpCode;
            return $errorMessage; // Failure, return error string

        } catch (\Exception $e) {
            $this.logger->error('360NRS SMS Transport Exception: '.$e->getMessage());
            return $e->getMessage(); // Failure, return error string
        }
    }
}