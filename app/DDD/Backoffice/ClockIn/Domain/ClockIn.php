<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain;

use App\DDD\Backoffice\ClockIn\Domain\Event\ClockInCreatedEvent;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInId;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInType;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInTimestamp;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInNotes;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInLatitude;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInLongitude;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceId;
use App\DDD\Shared\Domain\Event\RecordsDomainEvents;

class ClockIn
{
    use RecordsDomainEvents;

    protected function __construct(
        private readonly ClockInId $id,
        private readonly EmployeeId $employeeId,
        private readonly ClockInType $type,
        private readonly ClockInTimestamp $timestamp,
        private readonly ?WorkplaceId $workplaceId,
        private readonly ClockInLatitude $latitude,
        private readonly ClockInLongitude $longitude,
        private readonly ?ClockInNotes $notes,
        private readonly ClockInTimestamp $createdAt,
        private ?ClockInTimestamp $updatedAt,
    ) {}

    public static function create(
        ClockInId $id,
        EmployeeId $employeeId,
        ClockInType $type,
        ClockInTimestamp $timestamp,
        ClockInLatitude $latitude,
        ClockInLongitude $longitude,
        ?WorkplaceId $workplaceId = null,
        ?ClockInNotes $notes = null,
    ): self {
        $now = ClockInTimestamp::now();

        $clockIn = new self(
            id: $id,
            employeeId: $employeeId,
            type: $type,
            timestamp: $timestamp,
            workplaceId: $workplaceId,
            latitude: $latitude,
            longitude: $longitude,
            notes: $notes,
            createdAt: $now,
            updatedAt: null,
        );

        $clockIn->recordEvent(new ClockInCreatedEvent(
            aggregateId: (string) $id->value(),
            employeeId: $employeeId->value(),
            type: $type->value,
            timestamp: $timestamp->toString(),
            latitude: $latitude->value(),
            longitude: $longitude->value(),
            workplaceId: $workplaceId?->value(),
        ));

        return $clockIn;
    }

    public static function fromPrimitives(array $data): self
    {
        return new self(
            id: ClockInId::create($data['id']),
            employeeId: EmployeeId::create($data['employee_id']),
            type: ClockInType::from($data['type']),
            timestamp: ClockInTimestamp::fromString($data['timestamp']),
            workplaceId: isset($data['workplace_id']) ? WorkplaceId::create($data['workplace_id']) : null,
            latitude: isset($data['latitude']) ? ClockInLatitude::create($data['latitude']) : null,
            longitude: isset($data['longitude']) ? ClockInLongitude::create($data['longitude']) : null,
            notes: isset($data['notes']) ? ClockInNotes::create($data['notes']) : null,
            createdAt: ClockInTimestamp::fromString($data['created_at']),
            updatedAt: isset($data['updated_at']) ? ClockInTimestamp::fromString($data['updated_at']) : null,
        );
    }

    public function id(): ClockInId
    {
        return $this->id;
    }

    public function employeeId(): EmployeeId
    {
        return $this->employeeId;
    }

    public function type(): ClockInType
    {
        return $this->type;
    }

    public function timestamp(): ClockInTimestamp
    {
        return $this->timestamp;
    }

    public function workplaceId(): ?WorkplaceId
    {
        return $this->workplaceId;
    }

    public function latitude(): ClockInLatitude
    {
        return $this->latitude;
    }

    public function longitude(): ClockInLongitude
    {
        return $this->longitude;
    }

    public function notes(): ?ClockInNotes
    {
        return $this->notes;
    }

    public function createdAt(): ClockInTimestamp
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?ClockInTimestamp
    {
        return $this->updatedAt;
    }

    public function isEntry(): bool
    {
        return $this->type->isEntry();
    }

    public function isExit(): bool
    {
        return $this->type->isExit();
    }

    public function isBreak(): bool
    {
        return $this->type->isBreakStart() || $this->type->isBreakEnd();
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id->value(),
            'employee_id' => $this->employeeId->value(),
            'type' => $this->type->value,
            'timestamp' => $this->timestamp->toString(),
            'workplace_id' => $this->workplaceId?->value(),
            'latitude' => $this->latitude->value(),
            'longitude' => $this->longitude->value(),
            'notes' => $this->notes?->value(),
            'created_at' => $this->createdAt->toString(),
            'updated_at' => $this->updatedAt?->toString(),
        ];
    }
}
