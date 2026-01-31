<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\Service;

/**
 * Horas trabajadas en un día específico
 */
final class DayWorkedHours
{
    public function __construct(
        public readonly int $workedMinutes,
        public readonly int $breakMinutes,
        public readonly int $netMinutes,
        public readonly ?string $firstEntry,
        public readonly ?string $lastExit,
        public readonly bool $hasIncompleteEntry,
    ) {}

    public function workedHours(): float
    {
        return $this->workedMinutes / 60;
    }

    public function netHours(): float
    {
        return $this->netMinutes / 60;
    }

    public function toArray(): array
    {
        return [
            'worked_minutes' => $this->workedMinutes,
            'worked_hours' => $this->workedHours(),
            'break_minutes' => $this->breakMinutes,
            'net_minutes' => $this->netMinutes,
            'net_hours' => $this->netHours(),
            'first_entry' => $this->firstEntry,
            'last_exit' => $this->lastExit,
            'has_incomplete_entry' => $this->hasIncompleteEntry,
        ];
    }
}
