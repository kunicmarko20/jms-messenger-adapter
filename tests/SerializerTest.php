<?php

declare(strict_types=1);

namespace KunicMarko\JMSMessengerAdapter\Tests;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use KunicMarko\JMSMessengerAdapter\Exception\ArgumentMissing;
use KunicMarko\JMSMessengerAdapter\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Serializer as JMSSerializer;
use KunicMarko\JMSMessengerAdapter\Stamp\DeserializationContextStamp;
use KunicMarko\JMSMessengerAdapter\Stamp\SerializationContextStamp;
use KunicMarko\JMSMessengerAdapter\Tests\Fixtures\DummyMessage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\ValidationStamp;

final class SerializerTest extends TestCase
{
    /**
     * @var JMSSerializer
     */
    private $jmsSerializer;

    /** @test */
    public function encoded_is_decodable(): void
    {
        $serializer = new Serializer($this->jmsSerializer, 'json');

        $envelope = new Envelope(new DummyMessage('Hello'));

        $this->assertEquals($envelope, $serializer->decode($serializer->encode($envelope)));
    }

    /**
     * @test
     * @dataProvider decodeFailureData
     */
    public function decode_should_fail_if_arguments_missing(array $encodedEnvelope): void
    {
        $serializer = new Serializer($this->jmsSerializer, 'json');
        $this->expectException(ArgumentMissing::class);
        $serializer->decode($encodedEnvelope);
    }

    public function decodeFailureData(): \Generator
    {
        yield 'missing body' => [
            [],
        ];

        yield 'missing headers' => [
            [
                'body' => [
                    'a',
                ],
            ],
        ];

        yield 'missing header type' => [
            [
                'body' => [
                    'a',
                ],
                'headers' => [
                    'b',
                ],
            ],
        ];
    }

    /** @test */
    public function encoded_with_stamps_is_decodable(): void
    {
        $serializer = new Serializer($this->jmsSerializer, 'json');

        $envelope = (new Envelope(new DummyMessage('Hello')))
            ->with(new ValidationStamp(['foo', 'bar']))
        ;

        $this->assertEquals($envelope, $serializer->decode($serializer->encode($envelope)));
    }

    /** @test */
    public function encoded_is_having_the_body_and_type_header()
    {
        $serializer = new Serializer($this->jmsSerializer, 'json');

        $encoded = $serializer->encode(new Envelope(new DummyMessage('Hello')));

        $this->assertArrayHasKey('body', $encoded);
        $this->assertArrayHasKey('headers', $encoded);
        $this->assertArrayHasKey('type', $encoded['headers']);
        $this->assertEquals(DummyMessage::class, $encoded['headers']['type']);
    }

    /** @test */
    public function encoded_with_serializer_stamps()
    {
        $serializer = new Serializer($this->jmsSerializer, 'json');

        $envelope = (new Envelope(new DummyMessage('Hello')))
            ->with(new SerializationContextStamp(SerializationContext::create()))
            ->with(new DeserializationContextStamp(DeserializationContext::create()))
            ->with($validationStamp = new ValidationStamp(['foo', 'bar']))
        ;

        $encoded = $serializer->encode($envelope);

        $this->assertArrayHasKey('body', $encoded);
        $this->assertArrayHasKey('headers', $encoded);
        $this->assertArrayHasKey('type', $encoded['headers']);
        $this->assertArrayHasKey('X-Message-Stamp-'.SerializationContextStamp::class, $encoded['headers']);
        $this->assertArrayHasKey('X-Message-Stamp-'.ValidationStamp::class, $encoded['headers']);

        $decoded = $serializer->decode($encoded);

        $this->assertEquals($validationStamp, $decoded->last(ValidationStamp::class));
    }

    public function setUp(): void
    {
        $this->jmsSerializer = SerializerBuilder::create()
            ->addMetadataDir(__DIR__.'/../src/Resources/config/serializer')
            ->addMetadataDir(__DIR__.'/Fixtures/config', 'KunicMarko\JMSMessengerAdapter\Tests\Fixtures')
            ->build();
    }
}
