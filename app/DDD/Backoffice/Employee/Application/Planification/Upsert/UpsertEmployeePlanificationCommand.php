<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Planification\Upsert;

final class UpsertEmployeePlanificationCommand
{
    public function __construct(
        public readonly int $activeUserId,
        public readonly int $employeeId,
        public readonly array $weekSchedule,
    ) {}
}
