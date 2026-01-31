<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\Service;

/**
 * Resultado de comparar horas trabajadas vs planificación
 */
final class PlanificationComparisonResult
{
    /**
     * @param LateArrival[] $lateArrivals
     * @param EarlyDeparture[] $earlyDepartures
     * @param string[] $missedDays
     */
    public function __construct(
        public readonly int $workedMinutes,
        public readonly int $expectedMinutes,
        public readonly int $differenceMinutes,
        public readonly array $lateArrivals,
        public readonly array $earlyDepartures,
        public readonly array $missedDays,
        public readonly bool $hasOvertime,
        public readonly bool $hasUndertime,
    ) {}

    public function workedHours(): float
    {
        return $this->workedMinutes / 60;
    }

    public function expectedHours(): float
    {
        return $this->expectedMinutes / 60;
    }

    public function differenceHours(): float
    {
        return $this->differenceMinutes / 60;
    }

    public function totalLateMinutes(): int
    {
        return array_sum(array_map(fn ($l) => $l->minutesLate, $this->lateArrivals));
    }

    public function totalEarlyMinutes(): int
    {
        return array_sum(array_map(fn ($e) => $e->minutesEarly, $this->earlyDepartures));
    }

    public function toArray(): array
    {
        return [
            'worked_minutes' => $this->workedMinutes,
            'worked_hours' => $this->workedHours(),
            'expected_minutes' => $this->expectedMinutes,
            'expected_hours' => $this->expectedHours(),
            'difference_minutes' => $this->differenceMinutes,
            'difference_hours' => $this->differenceHours(),
            'has_overtime' => $this->hasOvertime,
            'has_undertime' => $this->hasUndertime,
            'late_arrivals' => array_map(fn ($l) => $l->toArray(), $this->lateArrivals),
            'early_departures' => array_map(fn ($e) => $e->toArray(), $this->earlyDepartures),
            'missed_days' => $this->missedDays,
            'total_late_minutes' => $this->totalLateMinutes(),
            'total_early_minutes' => $this->totalEarlyMinutes(),
        ];
    }
}
