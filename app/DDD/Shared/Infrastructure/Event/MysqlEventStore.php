<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Event;

use App\DDD\Shared\Domain\Event\DomainEvent;
use App\DDD\Shared\Domain\Event\StoredEvent;
use App\DDD\Shared\Infrastructure\Persistence\EloquentDomainEventModel;
use DateTimeImmutable;

/**
 * Almacena eventos en MySQL siguiendo el patrón Outbox
 */
final class MysqlEventStore
{
    public function __construct(
        private readonly ?EventEnricher $enricher = null,
    ) {}

    /**
     * Persiste eventos en la base de datos
     */
    public function append(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $metadata = $this->enricher?->enrich($event) ?? $this->buildBasicMetadata();

            EloquentDomainEventModel::create([
                'event_id' => $event->eventId(),
                'event_name' => $event::eventName(),
                'aggregate_id' => $event->aggregateId(),
                'payload' => $event->toPrimitives(),
                'metadata' => $metadata,
                'occurred_on' => $event->occurredOn(),
                'published_at' => null,
            ]);
        }
    }

    /**
     * Obtiene eventos pendientes de publicar (outbox)
     *
     * @return StoredEvent[]
     */
    public function getPendingEvents(int $limit = 100): array
    {
        return EloquentDomainEventModel::whereNull('published_at')
            ->orderBy('occurred_on')
            ->limit($limit)
            ->get()
            ->map(fn ($model) => $this->toStoredEvent($model))
            ->toArray();
    }

    /**
     * Marca eventos como publicados
     *
     * @param string[] $eventIds
     */
    public function markAsPublished(array $eventIds): void
    {
        EloquentDomainEventModel::whereIn('event_id', $eventIds)
            ->update(['published_at' => now()]);
    }

    /**
     * Marca un evento como publicado
     */
    public function markEventAsPublished(string $eventId): void
    {
        EloquentDomainEventModel::where('event_id', $eventId)
            ->update(['published_at' => now()]);
    }

    /**
     * Obtiene eventos por aggregate ID
     *
     * @return StoredEvent[]
     */
    public function getByAggregateId(string $aggregateId): array
    {
        return EloquentDomainEventModel::where('aggregate_id', $aggregateId)
            ->orderBy('occurred_on')
            ->get()
            ->map(fn ($model) => $this->toStoredEvent($model))
            ->toArray();
    }

    /**
     * Obtiene eventos por tipo
     *
     * @return StoredEvent[]
     */
    public function getByEventName(string $eventName, int $limit = 100): array
    {
        return EloquentDomainEventModel::where('event_name', $eventName)
            ->orderByDesc('occurred_on')
            ->limit($limit)
            ->get()
            ->map(fn ($model) => $this->toStoredEvent($model))
            ->toArray();
    }

    /**
     * Obtiene eventos en un rango de fechas
     *
     * @return StoredEvent[]
     */
    public function getByDateRange(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        ?string $eventName = null,
        int $limit = 1000,
    ): array {
        $query = EloquentDomainEventModel::whereBetween('occurred_on', [
            $from->format('Y-m-d H:i:s'),
            $to->format('Y-m-d H:i:s'),
        ]);

        if ($eventName !== null) {
            $query->where('event_name', $eventName);
        }

        return $query->orderBy('occurred_on')
            ->limit($limit)
            ->get()
            ->map(fn ($model) => $this->toStoredEvent($model))
            ->toArray();
    }

    /**
     * Búsqueda con filtros
     *
     * @return StoredEvent[]
     */
    public function search(array $filters, int $limit = 100, int $offset = 0): array
    {
        $query = EloquentDomainEventModel::query();

        if (isset($filters['event_name'])) {
            $query->where('event_name', $filters['event_name']);
        }

        if (isset($filters['aggregate_id'])) {
            $query->where('aggregate_id', $filters['aggregate_id']);
        }

        if (isset($filters['from'])) {
            $query->where('occurred_on', '>=', $filters['from']);
        }

        if (isset($filters['to'])) {
            $query->where('occurred_on', '<=', $filters['to']);
        }

        if (isset($filters['published'])) {
            if ($filters['published']) {
                $query->whereNotNull('published_at');
            } else {
                $query->whereNull('published_at');
            }
        }

        if (isset($filters['user_id'])) {
            $query->where('metadata->user->id', $filters['user_id']);
        }

        if (isset($filters['correlation_id'])) {
            $query->where('metadata->correlation_id', $filters['correlation_id']);
        }

        return $query->orderByDesc('occurred_on')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(fn ($model) => $this->toStoredEvent($model))
            ->toArray();
    }

    /**
     * Cuenta eventos pendientes
     */
    public function countPending(): int
    {
        return EloquentDomainEventModel::whereNull('published_at')->count();
    }

    /**
     * Cuenta total de eventos
     */
    public function countAll(): int
    {
        return EloquentDomainEventModel::count();
    }

    /**
     * Re-encola eventos para volver a publicar (replay)
     *
     * @param string[] $eventIds
     */
    public function requeueEvents(array $eventIds): int
    {
        return EloquentDomainEventModel::whereIn('event_id', $eventIds)
            ->update(['published_at' => null]);
    }

    /**
     * Re-encola eventos por rango de fechas
     */
    public function requeueByDateRange(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        ?string $eventName = null,
    ): int {
        $query = EloquentDomainEventModel::whereBetween('occurred_on', [
            $from->format('Y-m-d H:i:s'),
            $to->format('Y-m-d H:i:s'),
        ]);

        if ($eventName !== null) {
            $query->where('event_name', $eventName);
        }

        return $query->update(['published_at' => null]);
    }

    /**
     * Busca eventos por correlation_id
     *
     * @return StoredEvent[]
     */
    public function getByCorrelationId(string $correlationId): array
    {
        return EloquentDomainEventModel::where('metadata->correlation_id', $correlationId)
            ->orderBy('occurred_on')
            ->get()
            ->map(fn ($model) => $this->toStoredEvent($model))
            ->toArray();
    }

    private function toStoredEvent(EloquentDomainEventModel $model): StoredEvent
    {
        return new StoredEvent(
            eventId: $model->event_id,
            eventName: $model->event_name,
            aggregateId: $model->aggregate_id,
            payload: $model->payload,
            occurredOn: new DateTimeImmutable($model->occurred_on->format('Y-m-d H:i:s.u')),
            metadata: $model->metadata,
        );
    }

    private function buildBasicMetadata(): array
    {
        return [
            'ip' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
