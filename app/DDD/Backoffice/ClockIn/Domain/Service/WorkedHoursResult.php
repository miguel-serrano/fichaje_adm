<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\Service;

/**
 * Resultado del cálculo de horas trabajadas
 */
final class WorkedHoursResult
{
    /**
     * @param array<string, DayWorkedHours> $dailyHours
     * @param string[] $incompleteEntries
     */
    public function __construct(
        public readonly int $totalMinutes,
        public readonly int $breakMinutes,
        public readonly int $netWorkedMinutes,
        public readonly array $dailyHours,
        public readonly array $incompleteEntries,
    ) {}

    public function totalHours(): float
    {
        return $this->totalMinutes / 60;
    }

    public function netWorkedHours(): float
    {
        return $this->netWorkedMinutes / 60;
    }

    public function breakHours(): float
    {
        return $this->breakMinutes / 60;
    }

    public function hasIncompleteEntries(): bool
    {
        return !empty($this->incompleteEntries);
    }

    public function toArray(): array
    {
        return [
            'total_minutes' => $this->totalMinutes,
            'total_hours' => $this->totalHours(),
            'break_minutes' => $this->breakMinutes,
            'break_hours' => $this->breakHours(),
            'net_worked_minutes' => $this->netWorkedMinutes,
            'net_worked_hours' => $this->netWorkedHours(),
            'daily_hours' => array_map(fn ($d) => $d->toArray(), $this->dailyHours),
            'incomplete_entries' => $this->incompleteEntries,
        ];
    }
}
