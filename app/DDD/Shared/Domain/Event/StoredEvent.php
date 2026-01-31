<?php

declare(strict_types=1);

namespace App\DDD\Shared\Domain\Event;

use DateTimeImmutable;

/**
 * Representa un evento de dominio ya almacenado
 */
final class StoredEvent
{
    public function __construct(
        public readonly string $eventId,
        public readonly string $eventName,
        public readonly string $aggregateId,
        public readonly array $payload,
        public readonly DateTimeImmutable $occurredOn,
        public readonly ?array $metadata = null,
    ) {}

    public static function fromDomainEvent(DomainEvent $event, ?array $metadata = null): self
    {
        return new self(
            eventId: $event->eventId(),
            eventName: $event::eventName(),
            aggregateId: $event->aggregateId(),
            payload: $event->toPrimitives(),
            occurredOn: $event->occurredOn(),
            metadata: $metadata,
        );
    }

    public function toArray(): array
    {
        return [
            'event_id' => $this->eventId,
            'event_name' => $this->eventName,
            'aggregate_id' => $this->aggregateId,
            'payload' => $this->payload,
            'occurred_on' => $this->occurredOn->format('Y-m-d\TH:i:s.uP'),
            'metadata' => $this->metadata,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            eventId: $data['event_id'],
            eventName: $data['event_name'],
            aggregateId: $data['aggregate_id'],
            payload: $data['payload'],
            occurredOn: new DateTimeImmutable($data['occurred_on']),
            metadata: $data['metadata'] ?? null,
        );
    }
}
