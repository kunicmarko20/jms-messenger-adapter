<?php

namespace KunicMarko\JMSMessengerAdapter\Tests\Bridge\Symfony;

use KunicMarko\JMSMessengerAdapter\Bridge\Symfony\DependencyInjection\JMSMessengerAdapterExtension;
use KunicMarko\JMSMessengerAdapter\Bridge\Symfony\JMSMessengerAdapterBundle;
use PHPUnit\Framework\TestCase;

final class JMSMessengerAdapterBundleTest extends TestCase
{
    /**
     * @var JMSMessengerAdapterBundle
     */
    private $bundle;

    /** @test */
    public function it_returns_extension(): void
    {
        $this->assertInstanceOf(JMSMessengerAdapterExtension::class, $this->bundle->getContainerExtension());
    }

    protected function setUp(): void
    {
        $this->bundle = new JMSMessengerAdapterBundle();
    }
}
