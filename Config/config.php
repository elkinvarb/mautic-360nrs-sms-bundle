<?php
declare(strict_types=1);

return [
    // La información básica va DENTRO de la clave 'config'
    'config' => [
        'name'        => '360NRS SMS',
        'description' => 'Enables sending SMS via the 360NRS API.',
        'version'     => '4.0.0', // La versión final!
        'author'      => 'Viajes Éxito - IA GEMINI',
    ],

    // Las definiciones de servicios, rutas y menú van FUERA de la clave 'config'
    'routes' => [],
    'menu'   => [],

    'services' => [
        'events'       => [],
        'forms'        => [],
        'helpers'      => [],
        'models'       => [],
        'other' => [
            'mautic.sms.transport.nrssms' => [
                'class'     => \MauticPlugin\MauticNrsSmsBundle\Transport\NrsSmsTransport::class,
                'arguments' => ['mautic.helper.integration', 'monolog.logger.mautic'],
                'tag'          => 'mautic.sms_transport',
                'tagArguments' => ['integrationAlias' => 'NrsSms'],
            ],
        ],
        'integrations' => [
            'mautic.integration.nrssms' => [
                'class'     => \MauticPlugin\MauticNrsSmsBundle\Integration\NrsSmsIntegration::class,
                'arguments' => [
                    'event_dispatcher', 'mautic.helper.cache_storage', 'doctrine.orm.entity_manager',
                    'request_stack', 'router', 'translator', 'monolog.logger.mautic',
                    'mautic.helper.encryption', 'mautic.lead.model.lead', 'mautic.lead.model.company',
                    'mautic.helper.paths', 'mautic.core.model.notification', 'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity', 'mautic.lead.model.dnc',
                    'mautic.lead.field.fields_with_unique_identifier',
                ],
            ],
        ],
    ],
    'parameters' => [],
];