<?php

namespace KunicMarko\JMSMessengerAdapter\Bridge\Symfony\DependencyInjection;

use KunicMarko\JMSMessengerAdapter\Serializer;
use JMS\Serializer\SerializerInterface as JMSSerializer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

final class JMSMessengerAdapterExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $container->setDefinition(
            Serializer::class,
            new Definition(
                Serializer::class,
                [
                    new Reference(JMSSerializer::class),
                    $config['format'],
                ]
            )
        );

        $container->setAlias($config['serializer_id'], Serializer::class);
    }

    public function getAlias(): string
    {
        return 'jms_messenger';
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('jms_serializer')) {
            return;
        }

        $container->prependExtensionConfig('jms_serializer', [
            'metadata' => [
                'directories' => [
                    'JMSMessengerAdapter' => [
                        'path' => __DIR__.'/../../../Resources/config/serializer',
                    ],
                ],
            ],
        ]);
    }
}
