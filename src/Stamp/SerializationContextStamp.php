<?php

namespace KunicMarko\JMSMessengerAdapter\Stamp;

use JMS\Serializer\SerializationContext;
use Symfony\Component\Messenger\Stamp\StampInterface;

final class SerializationContextStamp implements StampInterface
{
    private $context;

    public function __construct(SerializationContext $context)
    {
        $this->context = $context;
    }

    public function getContext(): SerializationContext
    {
        return $this->context;
    }
}
