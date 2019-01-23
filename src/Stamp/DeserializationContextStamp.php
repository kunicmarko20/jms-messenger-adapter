<?php

namespace KunicMarko\JMSMessengerAdapter\Stamp;

use JMS\Serializer\DeserializationContext;
use Symfony\Component\Messenger\Stamp\StampInterface;

final class DeserializationContextStamp implements StampInterface
{
    private $context;

    public function __construct(DeserializationContext $context)
    {
        $this->context = $context;
    }

    public function getContext(): DeserializationContext
    {
        return $this->context;
    }
}
