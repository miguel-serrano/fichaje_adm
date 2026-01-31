<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\ValueObject;

/**
 * Representa el horario de un día, compuesto por uno o más turnos (TimeSlots)
 * Ejemplo: 09:00-14:00 y 15:00-18:00
 */
final class DaySchedule
{
    /**
     * @param TimeSlot[] $slots
     */
    private function __construct(
        private readonly array $slots,
        private readonly bool $isDayOff,
    ) {}

    /**
     * @param TimeSlot[] $slots
     */
    public static function create(array $slots): self
    {
        return new self($slots, empty($slots));
    }

    public static function dayOff(): self
    {
        return new self([], true);
    }

    public static function fromPrimitives(array $data): self
    {
        if ($data['is_day_off'] ?? false) {
            return self::dayOff();
        }

        $slots = [];
        foreach ($data['slots'] ?? [] as $slotData) {
            $slots[] = TimeSlot::fromPrimitives($slotData);
        }

        return new self($slots, empty($slots));
    }

    /**
     * @return TimeSlot[]
     */
    public function slots(): array
    {
        return $this->slots;
    }

    public function isDayOff(): bool
    {
        return $this->isDayOff;
    }

    public function totalHours(): float
    {
        if ($this->isDayOff) {
            return 0.0;
        }

        $total = 0.0;

        foreach ($this->slots as $slot) {
            $total += $slot->hours();
        }

        return $total;
    }

    public function startTime(): ?string
    {
        if (empty($this->slots)) {
            return null;
        }

        return $this->slots[0]->startTime();
    }

    public function endTime(): ?string
    {
        if (empty($this->slots)) {
            return null;
        }

        return $this->slots[count($this->slots) - 1]->endTime();
    }

    public function toPrimitives(): array
    {
        return [
            'is_day_off' => $this->isDayOff,
            'slots' => array_map(
                fn (TimeSlot $slot) => $slot->toPrimitives(),
                $this->slots,
            ),
            'total_hours' => $this->totalHours(),
        ];
    }
}
