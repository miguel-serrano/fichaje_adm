<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Event;

use App\DDD\Shared\Domain\Event\StoredEvent;

/**
 * Servicio que sincroniza eventos de MySQL a Elasticsearch
 */
final class EventSynchronizer
{
    public function __construct(
        private readonly MysqlEventStore $mysqlEventStore,
        private readonly ElasticsearchEventStore $elasticsearchEventStore,
    ) {}

    /**
     * Sincroniza eventos pendientes a Elasticsearch
     *
     * @return array{synced: int, failed: int, errors: array}
     */
    public function syncPendingEvents(int $batchSize = 100): array
    {
        $synced = 0;
        $failed = 0;
        $errors = [];

        $pendingEvents = $this->mysqlEventStore->getPendingEvents($batchSize);

        foreach ($pendingEvents as $storedEvent) {
            try {
                $this->syncEvent($storedEvent);
                $this->mysqlEventStore->markEventAsPublished($storedEvent->eventId);
                $synced++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = [
                    'event_id' => $storedEvent->eventId,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'synced' => $synced,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    /**
     * Sincroniza un evento específico
     */
    public function syncEvent(StoredEvent $storedEvent): void
    {
        $this->elasticsearchEventStore->appendStoredEvent($storedEvent);
    }

    /**
     * Re-sincroniza eventos por rango de fechas
     *
     * @return array{requeued: int}
     */
    public function resyncByDateRange(
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
        ?string $eventName = null,
    ): array {
        $requeued = $this->mysqlEventStore->requeueByDateRange($from, $to, $eventName);

        return ['requeued' => $requeued];
    }

    /**
     * Re-sincroniza eventos específicos
     *
     * @param string[] $eventIds
     * @return array{requeued: int}
     */
    public function resyncEvents(array $eventIds): array
    {
        $requeued = $this->mysqlEventStore->requeueEvents($eventIds);

        return ['requeued' => $requeued];
    }

    /**
     * Obtiene estadísticas de sincronización
     */
    public function getStats(): array
    {
        return [
            'total_events' => $this->mysqlEventStore->countAll(),
            'pending_events' => $this->mysqlEventStore->countPending(),
        ];
    }
}
