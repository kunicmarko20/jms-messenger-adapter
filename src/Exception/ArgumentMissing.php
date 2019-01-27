<?php

declare(strict_types=1);

namespace KunicMarko\JMSMessengerAdapter\Exception;

final class ArgumentMissing extends \InvalidArgumentException implements JMSMessengerAdapterException
{
    public static function envelopeBodyAndHeaders(): self
    {
        return new self('Encoded envelope should have at least a "body" and some "headers".');
    }

    public static function typeHeader(): self
    {
        return new self('Encoded envelope does not have a "type" header.');
    }
}
