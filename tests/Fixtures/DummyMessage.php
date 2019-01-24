<?php

namespace KunicMarko\JMSMessengerAdapter\Tests\Fixtures;

final class DummyMessage
{
    /**
     * @var string
     */
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
