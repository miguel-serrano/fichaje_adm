<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Application\ListByEmployee;

final class ListClockInsByEmployeeQuery
{
    public function __construct(
        public readonly int $activeUserId,
        public readonly int $employeeId,
        public readonly string $startDate,
        public readonly string $endDate,
    ) {}
}
