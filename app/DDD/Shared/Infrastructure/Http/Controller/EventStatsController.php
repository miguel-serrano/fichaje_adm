<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Http\Controller;

use App\DDD\Shared\Infrastructure\Event\EventSynchronizer;
use Illuminate\Http\JsonResponse;

final class EventStatsController
{
    public function __construct(
        private readonly EventSynchronizer $synchronizer,
    ) {}

    public function __invoke(): JsonResponse
    {
        $stats = $this->synchronizer->getStats();

        return response()->json([
            'total_events' => $stats['total_events'],
            'pending_events' => $stats['pending_events'],
            'synced_events' => $stats['total_events'] - $stats['pending_events'],
            'sync_percentage' => $stats['total_events'] > 0
                ? round(($stats['total_events'] - $stats['pending_events']) / $stats['total_events'] * 100, 2)
                : 100,
        ]);
    }
}
