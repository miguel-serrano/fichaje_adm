<?php

declare(strict_types=1);

namespace App\DDD\Shared\Application\Event;

use App\DDD\Shared\Domain\Event\EventStoreInterface;
use App\DDD\Shared\Domain\Event\StoredEvent;

final class SearchEventsQueryHandler
{
    public function __construct(
        private readonly EventStoreInterface $eventStore,
    ) {}

    public function __invoke(SearchEventsQuery $query): SearchEventsResponse
    {
        $filters = [];

        if ($query->eventName !== null) {
            $filters['event_name'] = $query->eventName;
        }

        if ($query->aggregateId !== null) {
            $filters['aggregate_id'] = $query->aggregateId;
        }

        if ($query->from !== null) {
            $filters['from'] = $query->from;
        }

        if ($query->to !== null) {
            $filters['to'] = $query->to;
        }

        if ($query->payloadSearch !== null) {
            $filters['payload_search'] = $query->payloadSearch;
        }

        $events = $this->eventStore->search(
            filters: $filters,
            limit: $query->limit,
            offset: $query->offset,
        );

        $items = array_map(
            fn (StoredEvent $event) => EventItem::fromStoredEvent($event),
            $events,
        );

        return new SearchEventsResponse($items);
    }
}
