<?php

namespace MauticPlugin\MauticNrsSmsBundle\Integration\NrsSms;

use Mautic\PluginBundle\Helper\IntegrationHelper;
use Twilio\Exceptions\ConfigurationException; // Reutilizamos esta excepción, es adecuada

class Configuration
{
    private ?string $authToken = null;
    private ?string $fromName = null;

    public function __construct(
        private IntegrationHelper $integrationHelper
    ) {
    }

    /**
     * @throws ConfigurationException
     */
    public function getAuthToken(): string
    {
        $this->setConfiguration();

        return $this->authToken;
    }

    /**
     * @throws ConfigurationException
     */
    public function getFromName(): string
    {
        $this->setConfiguration();

        return $this->fromName;
    }

    /**
     * @throws ConfigurationException
     */
    private function setConfiguration(): void
    {
        if (null !== $this->authToken) {
            // Ya está configurado
            return;
        }

        $integration = $this->integrationHelper->getIntegrationObject('NrsSms');

        if (!$integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            throw new ConfigurationException('360NRS integration is not published or enabled.');
        }

        // Obtener "From Name" de la pestaña de Características
        $featureSettings = $integration->getIntegrationSettings()->getFeatureSettings();
        if (empty($featureSettings['from_name'])) {
            throw new ConfigurationException('From Name is not configured for 360NRS integration.');
        }
        $this->fromName = $featureSettings['from_name'];

        // Obtener el Auth Token de las credenciales
        $keys = $integration->getDecryptedApiKeys();
        if (empty($keys['username'])) {
            throw new ConfigurationException('Auth Token (username) is not configured for 360NRS integration.');
        }

        $this->authToken = $keys['username'];
    }
}