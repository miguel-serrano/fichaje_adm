<?php

declare(strict_types=1);

namespace App\DDD\Shared\Application\Event;

final class SearchEventsQuery
{
    public function __construct(
        public readonly ?string $eventName = null,
        public readonly ?string $aggregateId = null,
        public readonly ?string $from = null,
        public readonly ?string $to = null,
        public readonly ?string $payloadSearch = null,
        public readonly int $limit = 100,
        public readonly int $offset = 0,
    ) {}
}
