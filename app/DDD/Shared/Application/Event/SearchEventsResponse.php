<?php

declare(strict_types=1);

namespace App\DDD\Shared\Application\Event;

final class SearchEventsResponse
{
    /**
     * @param EventItem[] $items
     */
    public function __construct(
        public readonly array $items,
    ) {}

    public function toArray(): array
    {
        return [
            'items' => array_map(fn ($item) => $item->toArray(), $this->items),
            'count' => count($this->items),
        ];
    }
}
