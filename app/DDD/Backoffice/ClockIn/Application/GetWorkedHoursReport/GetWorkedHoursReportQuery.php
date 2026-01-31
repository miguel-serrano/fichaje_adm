<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Application\GetWorkedHoursReport;

final class GetWorkedHoursReportQuery
{
    public function __construct(
        public readonly int $activeUserId,
        public readonly int $employeeId,
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly bool $compareWithPlanification = true,
    ) {}
}
