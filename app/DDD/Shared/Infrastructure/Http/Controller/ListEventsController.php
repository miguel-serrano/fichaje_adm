<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Http\Controller;

use App\DDD\Shared\Infrastructure\Event\MysqlEventStore;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ListEventsController
{
    public function __construct(
        private readonly MysqlEventStore $eventStore,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $filters = [];

        if ($request->has('event_name')) {
            $filters['event_name'] = $request->query('event_name');
        }

        if ($request->has('aggregate_id')) {
            $filters['aggregate_id'] = $request->query('aggregate_id');
        }

        if ($request->has('from')) {
            $filters['from'] = $request->query('from');
        }

        if ($request->has('to')) {
            $filters['to'] = $request->query('to');
        }

        if ($request->has('published')) {
            $filters['published'] = filter_var($request->query('published'), FILTER_VALIDATE_BOOLEAN);
        }

        $events = $this->eventStore->search(
            filters: $filters,
            limit: (int) $request->query('limit', 100),
            offset: (int) $request->query('offset', 0),
        );

        return response()->json([
            'data' => array_map(fn ($e) => $e->toArray(), $events),
            'count' => count($events),
            'filters' => $filters,
        ]);
    }
}
