<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\Entity;

use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeePlanificationId;
use App\DDD\Backoffice\Employee\Domain\ValueObject\WeekSchedule;
use App\DDD\Backoffice\Employee\Domain\ValueObject\DaySchedule;

class EmployeePlanification
{
    protected function __construct(
        private readonly EmployeePlanificationId $id,
        private readonly WeekSchedule $weekSchedule,
        private readonly float $totalWeekHours,
    ) {}

    public static function create(
        EmployeePlanificationId $id,
        WeekSchedule $weekSchedule,
    ): self {
        return new self(
            id: $id,
            weekSchedule: $weekSchedule,
            totalWeekHours: $weekSchedule->totalHours(),
        );
    }

    public static function fromPrimitives(array $data): self
    {
        $weekSchedule = WeekSchedule::fromPrimitives($data['week_schedule']);

        return new self(
            id: EmployeePlanificationId::create($data['id']),
            weekSchedule: $weekSchedule,
            totalWeekHours: $data['total_week_hours'] ?? $weekSchedule->totalHours(),
        );
    }

    public function id(): EmployeePlanificationId
    {
        return $this->id;
    }

    public function weekSchedule(): WeekSchedule
    {
        return $this->weekSchedule;
    }

    public function totalWeekHours(): float
    {
        return $this->totalWeekHours;
    }

    public function getScheduleForDay(int $dayOfWeek): ?DaySchedule
    {
        return $this->weekSchedule->getDay($dayOfWeek);
    }

    public function isDayOff(int $dayOfWeek): bool
    {
        $schedule = $this->getScheduleForDay($dayOfWeek);
        return $schedule === null || $schedule->isDayOff();
    }

    public function expectedHoursForDay(int $dayOfWeek): float
    {
        $schedule = $this->getScheduleForDay($dayOfWeek);
        return $schedule?->totalHours() ?? 0.0;
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id->value(),
            'week_schedule' => $this->weekSchedule->toPrimitives(),
            'total_week_hours' => $this->totalWeekHours,
        ];
    }
}
