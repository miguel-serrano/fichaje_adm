<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Http\Controller;

use App\DDD\Shared\Infrastructure\Event\EventSynchronizer;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SyncEventsController
{
    public function __construct(
        private readonly EventSynchronizer $synchronizer,
    ) {}

    /**
     * POST /api/events/sync
     * Sincroniza eventos pendientes a Elasticsearch
     */
    public function sync(Request $request): JsonResponse
    {
        $batchSize = (int) $request->input('batch_size', 100);

        $result = $this->synchronizer->syncPendingEvents($batchSize);

        return response()->json([
            'message' => 'Sincronización completada',
            'synced' => $result['synced'],
            'failed' => $result['failed'],
            'errors' => $result['errors'],
            'stats' => $this->synchronizer->getStats(),
        ]);
    }

    /**
     * POST /api/events/resync
     * Re-encola eventos para volver a sincronizarlos
     */
    public function resync(Request $request): JsonResponse
    {
        $request->validate([
            'event_ids' => 'nullable|array',
            'event_ids.*' => 'string',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'event_name' => 'nullable|string',
        ]);

        $eventIds = $request->input('event_ids');

        if (!empty($eventIds)) {
            $result = $this->synchronizer->resyncEvents($eventIds);
        } else {
            $from = $request->input('from');
            $to = $request->input('to');

            if (!$from || !$to) {
                return response()->json([
                    'error' => 'Debes proporcionar event_ids o from/to',
                ], 400);
            }

            $result = $this->synchronizer->resyncByDateRange(
                from: new DateTimeImmutable($from),
                to: new DateTimeImmutable($to . ' 23:59:59'),
                eventName: $request->input('event_name'),
            );
        }

        return response()->json([
            'message' => 'Eventos re-encolados',
            'requeued' => $result['requeued'],
            'stats' => $this->synchronizer->getStats(),
        ]);
    }
}
