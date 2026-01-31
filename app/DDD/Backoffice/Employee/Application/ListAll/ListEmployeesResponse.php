<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\ListAll;

final class ListEmployeesResponse
{
    /**
     * @param EmployeeItem[] $items
     */
    public function __construct(
        public readonly array $items,
    ) {}

    public function toArray(): array
    {
        return array_map(
            fn (EmployeeItem $item) => $item->toArray(),
            $this->items,
        );
    }
}
