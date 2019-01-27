<?php

declare(strict_types=1);

namespace KunicMarko\JMSMessengerAdapter\Tests\Bridge\Symfony\DependencyInjection;

use KunicMarko\JMSMessengerAdapter\Bridge\Symfony\DependencyInjection\JMSMessengerAdapterExtension;
use KunicMarko\JMSMessengerAdapter\Serializer;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

final class JMSMessengerAdapterExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_has_serializer_service(): void
    {
        $this->load();
        $this->assertContainerBuilderHasService(Serializer::class, Serializer::class);
        $this->assertContainerBuilderHasAlias('messenger.transport.jms_serializer', Serializer::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new JMSMessengerAdapterExtension()];
    }
}
