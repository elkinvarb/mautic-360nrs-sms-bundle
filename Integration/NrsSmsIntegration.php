<?php

namespace MauticPlugin\MauticNrsSmsBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;

/**
 * Manages the configuration form for the 360NRS integration in Mautic's UI.
 */
class NrsSmsIntegration extends AbstractIntegration
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName(): string
    {
        return 'NrsSms';
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return '360NRS SMS';
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getAuthenticationType(): string
    {
        return 'key';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, string>
     */
    public function getRequiredKeyFields(): array
    {
        // Mautic's 'key' auth type uses 'username' internally for the API Key field.
        return [
            'username' => 'mautic.integration.nrssms.auth_token',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function appendToForm(&$builder, $data, $formArea): void
    {
        if ('features' === $formArea) {
            $builder->add(
                'from_name',
                TextType::class,
                [
                    'label' => 'mautic.integration.nrssms.from_name',
                    'attr'  => [
                        'class'   => 'form-control',
                        'tooltip' => 'mautic.integration.nrssms.from_name.tooltip',
                    ],
                    'required' => true,
                ]
            );
        }
    }
}