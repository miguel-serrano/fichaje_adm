<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Http\Controller;

use App\DDD\Shared\Infrastructure\Event\MysqlEventStore;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

final class ListDomainEventsController extends Controller
{
    public function __construct(
        private readonly MysqlEventStore $mysqlEventStore,
    ) {}

    public function __invoke(Request $request): Response
    {
        $filters = [];

        if ($request->filled('event_name')) {
            $filters['event_name'] = $request->string('event_name')->toString();
        }

        if ($request->filled('aggregate_id')) {
            $filters['aggregate_id'] = $request->string('aggregate_id')->toString();
        }

        $limit = $request->integer('limit', 50);
        $offset = $request->integer('offset', 0);

        $events = $this->mysqlEventStore->search($filters, $limit, $offset);
        $totalEvents = $this->mysqlEventStore->countAll();
        $pendingEvents = $this->mysqlEventStore->countPending();

        $items = array_map(fn ($event) => $event->toArray(), $events);

        return Inertia::render('Backoffice/Events/Index', [
            'events' => $items,
            'totalEvents' => $totalEvents,
            'pendingEvents' => $pendingEvents,
            'filters' => [
                'event_name' => $request->input('event_name', ''),
                'aggregate_id' => $request->input('aggregate_id', ''),
            ],
        ]);
    }
}
