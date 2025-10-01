<?php

return [
    'name'        => '360NRS SMS',
    'description' => 'Enables sending SMS via the 360NRS API.',
    'version'     => '1.1.0',
    'author'      => 'VIAJES ÉXITO - IA GEMINI',

    'services' => [
        'integrations' => [
            'mautic.integration.nrssms' => [
                'class'     => MauticPlugin\MauticNrsSmsBundle\Integration\NrsSmsIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'request_stack',
                    'router',
                    'translator',
                    'monolog.logger.mautic',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                    'mautic.lead.field.fields_with_unique_identifier',
                ],
            ],
        ],
        'sms_transports' => [
            'mautic.sms.transport.nrssms' => [
                'class'     => \MauticPlugin\MauticNrsSmsBundle\Transport\NrsSmsTransport::class,
                'arguments' => [
                    'mautic.helper.integration',
                    'monolog.logger.mautic',
                ],
                'alias'     => 'sms_360nrs',
                'label'     => '360NRS SMS',
                
                // This tag is crucial for Mautic's TransportChain to discover this service.
                'tag'          => 'mautic.sms_transport',
                'tagArguments' => [
                    // This links the transport to the integration to check its published status.
                    // 'NrsSms' must match the return of getName() in NrsSmsIntegration.php
                    'integrationAlias' => 'NrsSms',
                ],
            ],
        ],
    ],
];