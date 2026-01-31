<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\Service;

/**
 * Representa una salida anticipada
 */
final class EarlyDeparture
{
    public function __construct(
        public readonly string $date,
        public readonly string $expectedTime,
        public readonly string $actualTime,
        public readonly int $minutesEarly,
    ) {}

    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'expected_time' => $this->expectedTime,
            'actual_time' => $this->actualTime,
            'minutes_early' => $this->minutesEarly,
        ];
    }
}
