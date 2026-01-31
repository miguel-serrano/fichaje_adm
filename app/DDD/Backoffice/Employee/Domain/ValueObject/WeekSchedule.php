<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\ValueObject;

/**
 * Representa el horario semanal de un empleado
 * Los días van de 1 (Lunes) a 7 (Domingo) siguiendo ISO-8601
 */
final class WeekSchedule
{
    private const MONDAY = 1;
    private const TUESDAY = 2;
    private const WEDNESDAY = 3;
    private const THURSDAY = 4;
    private const FRIDAY = 5;
    private const SATURDAY = 6;
    private const SUNDAY = 7;

    /**
     * @param array<int, DaySchedule> $days Indexado por día de la semana (1-7)
     */
    private function __construct(
        private readonly array $days,
    ) {}

    public static function create(array $days): self
    {
        return new self($days);
    }

    public static function fromPrimitives(array $data): self
    {
        $days = [];

        foreach ($data as $dayNumber => $dayData) {
            if ($dayData !== null) {
                $days[(int) $dayNumber] = DaySchedule::fromPrimitives($dayData);
            }
        }

        return new self($days);
    }

    /**
     * Crea un horario estándar de oficina (Lun-Vie 9-14, 15-18)
     */
    public static function standardOfficeHours(): self
    {
        $officeDay = DaySchedule::create([
            TimeSlot::create('09:00', '14:00'),
            TimeSlot::create('15:00', '18:00'),
        ]);

        return new self([
            self::MONDAY => $officeDay,
            self::TUESDAY => $officeDay,
            self::WEDNESDAY => $officeDay,
            self::THURSDAY => $officeDay,
            self::FRIDAY => $officeDay,
            self::SATURDAY => DaySchedule::dayOff(),
            self::SUNDAY => DaySchedule::dayOff(),
        ]);
    }

    public function getDay(int $dayOfWeek): ?DaySchedule
    {
        return $this->days[$dayOfWeek] ?? null;
    }

    public function totalHours(): float
    {
        $total = 0.0;

        foreach ($this->days as $day) {
            $total += $day->totalHours();
        }

        return $total;
    }

    public function workingDays(): int
    {
        $count = 0;

        foreach ($this->days as $day) {
            if (!$day->isDayOff()) {
                $count++;
            }
        }

        return $count;
    }

    public function toPrimitives(): array
    {
        $data = [];

        foreach ($this->days as $dayNumber => $day) {
            $data[$dayNumber] = $day->toPrimitives();
        }

        return $data;
    }
}
