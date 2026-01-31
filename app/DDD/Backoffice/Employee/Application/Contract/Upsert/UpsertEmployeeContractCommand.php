<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Contract\Upsert;

final class UpsertEmployeeContractCommand
{
    public function __construct(
        public readonly int $activeUserId,
        public readonly int $employeeId,
        public readonly string $type,
        public readonly string $startDate,
        public readonly ?string $endDate,
        public readonly float $hoursPerWeek,
    ) {}
}
