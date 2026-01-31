<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Application\ListByEmployee;

final class ListClockInsByEmployeeResponse
{
    /**
     * @param ClockInItem[] $items
     */
    private function __construct(
        public readonly array $items,
    ) {}

    public static function create(array $clockIns): self
    {
        return new self(
            items: array_map(
                fn (array $clockIn) => ClockInItem::fromArray($clockIn),
                $clockIns,
            ),
        );
    }

    public function toArray(): array
    {
        return array_map(
            fn (ClockInItem $item) => $item->toArray(),
            $this->items,
        );
    }
}
