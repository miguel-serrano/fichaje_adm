<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Application\ListByEmployee;

final class ClockInItem
{
    private function __construct(
        public readonly int $id,
        public readonly int $employeeId,
        public readonly string $type,
        public readonly string $typeLabel,
        public readonly string $timestamp,
        public readonly string $date,
        public readonly string $time,
        public readonly ?int $workplaceId,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            employeeId: $data['employee_id'],
            type: $data['type'],
            typeLabel: $data['type_label'],
            timestamp: $data['timestamp'],
            date: $data['date'],
            time: $data['time'],
            workplaceId: $data['workplace_id'] ?? null,
            latitude: $data['latitude'] ?? null,
            longitude: $data['longitude'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employeeId,
            'type' => $this->type,
            'type_label' => $this->typeLabel,
            'timestamp' => $this->timestamp,
            'date' => $this->date,
            'time' => $this->time,
            'workplace_id' => $this->workplaceId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'notes' => $this->notes,
        ];
    }
}
