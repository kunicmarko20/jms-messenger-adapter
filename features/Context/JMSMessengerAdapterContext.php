<?php

namespace KunicMarko\JMSMessengerAdapter\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Symfony2Extension\Context\KernelDictionary;
use PHPUnit\Framework\Assert;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use KunicMarko\JMSMessengerAdapter\Features\Fixtures\Project\Query\DoesItWork;

class JMSMessengerAdapterContext implements Context
{
    use KernelDictionary;

    private $messageBus;

    private $receiverLocator;

    public function __construct(MessageBusInterface $messageBus, ContainerInterface $receiverLocator)
    {
        $this->messageBus = $messageBus;
        $this->receiverLocator = $receiverLocator;
    }

    /**
     * @When I dispatch a query
     */
    public function iDispatchAQuery()
    {
        $this->messageBus->dispatch(new DoesItWork('works'));
    }

    /**
     * @Then I should get a response with missing field
     */
    public function iShouldGetAResponseWithMissingField()
    {
        $receiver = $this->receiverLocator->get('amqp');

        $receiver->receive(static function (Envelope $envelope) use ($receiver): void {
            $message = $envelope->getMessage();
            Assert::assertInstanceOf(DoesItWork::class, $message);
            Assert::assertSame('works', $message->works);
            Assert::assertNull($message->notExposed);
            Assert::assertNull($message->shouldBeNull);
            $receiver->stop();
        });
    }

    /**
     * @When I dispatch a query with a group
     */
    public function iDispatchAQueryWithGroup()
    {
        $this->messageBus->dispatch(new DoesItWork('addGroup', 'notNull'));
    }

    /**
     * @Then I should get a response
     */
    public function iShouldGetAResponse()
    {
        $receiver = $this->receiverLocator->get('amqp');

        $receiver->receive(static function (Envelope $envelope) use ($receiver): void {
            $message = $envelope->getMessage();
            Assert::assertInstanceOf(DoesItWork::class, $message);
            Assert::assertNull($message->works);
            Assert::assertNull($message->notExposed);
            Assert::assertSame('notNull', $message->shouldBeNull);
            $receiver->stop();
        });
    }
}
