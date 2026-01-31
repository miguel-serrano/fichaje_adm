<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\Service;

/**
 * Representa un retraso en la entrada
 */
final class LateArrival
{
    public function __construct(
        public readonly string $date,
        public readonly string $expectedTime,
        public readonly string $actualTime,
        public readonly int $minutesLate,
    ) {}

    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'expected_time' => $this->expectedTime,
            'actual_time' => $this->actualTime,
            'minutes_late' => $this->minutesLate,
        ];
    }
}
