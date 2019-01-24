<?php

namespace KunicMarko\JMSMessengerAdapter\Tests\Stamp;

use JMS\Serializer\SerializationContext;
use KunicMarko\JMSMessengerAdapter\Stamp\SerializationContextStamp;
use PHPUnit\Framework\TestCase;

final class DeserializationContextStampTest extends TestCase
{
    public function testSerializable()
    {
        $context = SerializationContext::create();
        $context->setGroups(['foo']);

        $stamp = new SerializationContextStamp($context);

        $this->assertEquals($stamp, unserialize(serialize($stamp)));
    }
}
