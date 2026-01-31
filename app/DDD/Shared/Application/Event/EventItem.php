<?php

declare(strict_types=1);

namespace App\DDD\Shared\Application\Event;

use App\DDD\Shared\Domain\Event\StoredEvent;

final class EventItem
{
    private function __construct(
        public readonly string $eventId,
        public readonly string $eventName,
        public readonly string $aggregateId,
        public readonly array $payload,
        public readonly string $occurredOn,
        public readonly ?array $metadata,
    ) {}

    public static function fromStoredEvent(StoredEvent $event): self
    {
        return new self(
            eventId: $event->eventId,
            eventName: $event->eventName,
            aggregateId: $event->aggregateId,
            payload: $event->payload,
            occurredOn: $event->occurredOn->format('Y-m-d H:i:s'),
            metadata: $event->metadata,
        );
    }

    public function toArray(): array
    {
        return [
            'event_id' => $this->eventId,
            'event_name' => $this->eventName,
            'aggregate_id' => $this->aggregateId,
            'payload' => $this->payload,
            'occurred_on' => $this->occurredOn,
            'metadata' => $this->metadata,
        ];
    }
}
