<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\Event;

use App\DDD\Shared\Domain\Event\AbstractDomainEvent;

final class LateArrivalDetectedEvent extends AbstractDomainEvent
{
    public function __construct(
        string $aggregateId,
        private readonly int $employeeId,
        private readonly string $employeeName,
        private readonly string $date,
        private readonly int $minutesLate,
        private readonly string $expectedTime,
        private readonly string $actualTime,
    ) {
        parent::__construct($aggregateId);
    }

    public static function eventName(): string
    {
        return 'clock_in.late_arrival_detected';
    }

    public function employeeId(): int
    {
        return $this->employeeId;
    }

    public function employeeName(): string
    {
        return $this->employeeName;
    }

    public function date(): string
    {
        return $this->date;
    }

    public function minutesLate(): int
    {
        return $this->minutesLate;
    }

    public function expectedTime(): string
    {
        return $this->expectedTime;
    }

    public function actualTime(): string
    {
        return $this->actualTime;
    }

    public function toPrimitives(): array
    {
        return [
            'aggregate_id' => $this->aggregateId(),
            'employee_id' => $this->employeeId,
            'employee_name' => $this->employeeName,
            'date' => $this->date,
            'minutes_late' => $this->minutesLate,
            'expected_time' => $this->expectedTime,
            'actual_time' => $this->actualTime,
        ];
    }
}
