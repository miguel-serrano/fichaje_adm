<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Application\ListAll;

final class ListWorkplacesResponse
{
    /**
     * @param WorkplaceItem[] $items
     */
    public function __construct(
        public readonly array $items,
    ) {}

    public function toArray(): array
    {
        return array_map(
            fn (WorkplaceItem $item) => $item->toArray(),
            $this->items,
        );
    }
}
