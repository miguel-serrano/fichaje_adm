<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\ValueObject;

/**
 * Representa un turno de trabajo (ej: 09:00 - 14:00)
 */
final class TimeSlot
{
    private function __construct(
        private readonly string $startTime,
        private readonly string $endTime,
    ) {}

    public static function create(string $startTime, string $endTime): self
    {
        self::validateTime($startTime);
        self::validateTime($endTime);

        if ($startTime >= $endTime) {
            throw new \InvalidArgumentException(
                sprintf('Start time (%s) must be before end time (%s)', $startTime, $endTime)
            );
        }

        return new self($startTime, $endTime);
    }

    public static function fromPrimitives(array $data): self
    {
        return new self($data['start_time'], $data['end_time']);
    }

    public function startTime(): string
    {
        return $this->startTime;
    }

    public function endTime(): string
    {
        return $this->endTime;
    }

    public function hours(): float
    {
        $start = \DateTimeImmutable::createFromFormat('H:i', $this->startTime);
        $end = \DateTimeImmutable::createFromFormat('H:i', $this->endTime);

        $diff = $end->getTimestamp() - $start->getTimestamp();

        return $diff / 3600;
    }

    public function minutes(): int
    {
        return (int) ($this->hours() * 60);
    }

    public function contains(string $time): bool
    {
        return $time >= $this->startTime && $time <= $this->endTime;
    }

    public function toPrimitives(): array
    {
        return [
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'hours' => $this->hours(),
        ];
    }

    private static function validateTime(string $time): void
    {
        if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid time format: %s. Expected HH:MM', $time)
            );
        }
    }
}
