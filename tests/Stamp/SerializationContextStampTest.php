<?php

namespace KunicMarko\JMSMessengerAdapter\Tests\Stamp;

use JMS\Serializer\SerializationContext;
use KunicMarko\JMSMessengerAdapter\Stamp\SerializationContextStamp;
use PHPUnit\Framework\TestCase;

final class SerializationContextStampTest extends TestCase
{
    public function testSerializable()
    {
        $context = SerializationContext::create();
        $context->setSerializeNull(true);

        $stamp = new SerializationContextStamp($context);

        $this->assertEquals($stamp, unserialize(serialize($stamp)));
    }
}
