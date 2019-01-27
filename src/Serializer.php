<?php

declare(strict_types=1);

namespace KunicMarko\JMSMessengerAdapter;

use KunicMarko\JMSMessengerAdapter\Exception\ArgumentMissing;
use KunicMarko\JMSMessengerAdapter\Stamp\DeserializationContextStamp;
use KunicMarko\JMSMessengerAdapter\Stamp\SerializationContextStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use JMS\Serializer\SerializerInterface as JMSSerializerInterface;

final class Serializer implements SerializerInterface
{
    private const STAMP_HEADER_PREFIX = 'X-Message-Stamp-';

    private $serializer;

    private $format;

    public function __construct(JMSSerializerInterface $serializer, string $format)
    {
        $this->serializer = $serializer;
        $this->format = $format;
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        if (empty($encodedEnvelope['body']) || empty($encodedEnvelope['headers'])) {
            throw ArgumentMissing::envelopeBodyAndHeaders();
        }

        if (empty($encodedEnvelope['headers']['type'])) {
            throw ArgumentMissing::typeHeader();
        }

        $stamps = $this->decodeStamps($encodedEnvelope);

        if (isset($stamps[DeserializationContextStamp::class])) {
            $context = end($stamps[DeserializationContextStamp::class])->getContext();
        }

        $message = $this->serializer->deserialize(
            $encodedEnvelope['body'],
            $encodedEnvelope['headers']['type'],
            $this->format,
            $context ?? null
        );

        return new Envelope($message, ... $this->extractStamps($stamps));
    }

    private function extractStamps(array $stamps): array
    {
        $extractedStamps = [];

        foreach ($stamps as $innerStamps) {
            foreach ($innerStamps as $stamp) {
                $extractedStamps[] = $stamp;
            }
        }

        return $extractedStamps;
    }

    private function decodeStamps(array $encodedEnvelope): array
    {
        $stamps = [];

        foreach ($encodedEnvelope['headers'] as $name => $arrayValue) {
            if (strpos($name, self::STAMP_HEADER_PREFIX) !== 0) {
                continue;
            }

            $type = substr($name, \strlen(self::STAMP_HEADER_PREFIX));

            foreach ($arrayValue as $value) {
                $stamps[$type][] = $this->serializer->deserialize(
                    $value,
                    $type,
                    $this->format
                );
            }
        }

        return $stamps;
    }

    public function encode(Envelope $envelope): array
    {
        if ($serializerStamp = $envelope->last(SerializationContextStamp::class)) {
            \assert($serializerStamp instanceof SerializationContextStamp);
            $context = $serializerStamp->getContext();
        }

        $headers = ['type' => \get_class($envelope->getMessage())] + $this->encodeStamps($envelope);

        return [
            'body' => $this->serializer->serialize($envelope->getMessage(), $this->format, $context ?? null),
            'headers' => $headers,
        ];
    }

    private function encodeStamps(Envelope $envelope): array
    {
        if (!$allStamps = $envelope->all()) {
            return [];
        }

        $headers = [];

        foreach ($allStamps as $class => $stamps) {
            foreach ($stamps as $stamp) {
                $headers[self::STAMP_HEADER_PREFIX.$class][] = $this->serializer->serialize($stamp, $this->format);
            }
        }

        return $headers;
    }
}
