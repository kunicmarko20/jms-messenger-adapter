<?php

namespace KunicMarko\JMSMessengerAdapter\Features\Fixtures\Project\Middleware;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use KunicMarko\JMSMessengerAdapter\Features\Fixtures\Project\Query\DoesItWork;
use KunicMarko\JMSMessengerAdapter\Stamp\DeserializationContextStamp;
use KunicMarko\JMSMessengerAdapter\Stamp\SerializationContextStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class AddStampMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        if ($message instanceof DoesItWork && $message->works === 'addGroup') {
            $deserializationContext = DeserializationContext::create();
            $serializationContext = SerializationContext::create();
            $deserializationContext->setGroups(['canAnyoneSeeMe']);
            $deserializationContext->increaseDepth();
            $serializationContext->setGroups(['canAnyoneSeeMe']);
            $serializationContext->setSerializeNull(true);
            $envelope = $envelope->with(new DeserializationContextStamp($deserializationContext));
            $envelope = $envelope->with(new SerializationContextStamp($serializationContext));
        }

        return $stack->next()->handle($envelope, $stack);
    }
}