<?php

namespace KunicMarko\JMSMessengerAdapter\Bridge\Symfony\DependencyInjection;

use KunicMarko\JMSMessengerAdapter\Serializer;
use JMS\Serializer\SerializerInterface as JMSSerializer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\Messenger\Transport\AmqpExt\AmqpTransportFactory;

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

        $container->setAlias($config['id'], Serializer::class);

        if ($container->hasAlias('messenger.transport.serializer')) {
            $container->removeAlias('messenger.transport.serializer');
        }

        $container->setAlias('messenger.transport.serializer', $config['id']);


        if ($config['transports']['amqp']['enabled'] ?? false
            && !$container->has($config['transports']['amqp']['factory_id'])
        ) {
            $container->setDefinition(
                $config['transports']['amqp']['factory_id'],
                (new Definition(
                    AmqpTransportFactory::class,
                    [
                        new Reference(Serializer::class),
                        '%kernel.debug%'
                    ]
                ))->addTag('messenger.transport_factory')
            );
        }
    }

    public function getAlias(): string
    {
        return 'jms_messenger';
    }

    public function prepend(ContainerBuilder $container): void
    {
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
