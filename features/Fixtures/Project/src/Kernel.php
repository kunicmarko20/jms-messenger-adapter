<?php

declare(strict_types=1);

namespace KunicMarko\JMSMessengerAdapter\Features\Fixtures\Project;

use JMS\SerializerBundle\JMSSerializerBundle;
use KunicMarko\JMSMessengerAdapter\Bridge\Symfony\JMSMessengerAdapterBundle;
use KunicMarko\JMSMessengerAdapter\Features\Fixtures\Project\Middleware\AddStampMiddleware;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use KunicMarko\JMSMessengerAdapter\Features\Fixtures\Project\DependencyInjection\Compiler\ExposeServicesAsPublicForTestingCompilerPass;
use KunicMarko\JMSMessengerAdapter\Features\Fixtures\Project\Query\DoesItWork;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return __DIR__.'/..';
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/var/cache/test';
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/logs';
    }

    public function registerBundles(): \Generator
    {
        yield new FrameworkBundle();
        yield new JMSSerializerBundle();
        yield new JMSMessengerAdapterBundle();
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('kernel.secret', 'secret');

        $container->setDefinition(
            AddStampMiddleware::class,
            new Definition(AddStampMiddleware::class)
        );

        $container->prependExtensionConfig('framework', [
            'messenger' => [
                'transports' => [
                    'amqp' => 'amqp://guest:guest@localhost:5672/%2f/messages',
                ],
                'routing' => [
                    DoesItWork::class => 'amqp',
                ],
                'serializer' => [
                    'id' => 'messenger.transport.jms_serializer',
                ],
                'buses' => [
                    'messenger.bus.default' => [
                        'middleware' => [
                            'KunicMarko\JMSMessengerAdapter\Features\Fixtures\Project\Middleware\AddStampMiddleware',
                        ],
                    ],
                ],
            ],
        ]);

        $container->prependExtensionConfig('jms_serializer', [
            'metadata' => [
                'directories' => [
                    'not sure if this string is important' => [
                        'namespace_prefix' => 'KunicMarko\JMSMessengerAdapter\Features\Fixtures\Project',
                        'path' => '%kernel.root_dir%/../config/serializer',
                    ],
                ],
            ],
        ]);

        $container->addCompilerPass(new ExposeServicesAsPublicForTestingCompilerPass());
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
    }
}
