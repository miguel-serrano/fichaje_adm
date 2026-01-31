<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Application\GetWorkedHoursReport;

final class GetWorkedHoursReportResponse
{
    public function __construct(
        public readonly int $employeeId,
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly array $workedHours,
        public readonly ?array $comparison,
    ) {}

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employeeId,
            'period' => [
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
            ],
            'worked_hours' => $this->workedHours,
            'comparison' => $this->comparison,
        ];
    }
}
