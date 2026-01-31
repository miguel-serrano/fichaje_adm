<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\Event;

use App\DDD\Shared\Domain\Event\AbstractDomainEvent;

final class ClockInCreatedEvent extends AbstractDomainEvent
{
    public function __construct(
        string $aggregateId,
        private readonly int $employeeId,
        private readonly string $type,
        private readonly string $timestamp,
        private readonly float $latitude,
        private readonly float $longitude,
        private readonly ?int $workplaceId,
    ) {
        parent::__construct($aggregateId);
    }

    public static function eventName(): string
    {
        return 'clock_in.created';
    }

    public function employeeId(): int
    {
        return $this->employeeId;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function timestamp(): string
    {
        return $this->timestamp;
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function workplaceId(): ?int
    {
        return $this->workplaceId;
    }

    public function toPrimitives(): array
    {
        return [
            'aggregate_id' => $this->aggregateId(),
            'employee_id' => $this->employeeId,
            'type' => $this->type,
            'timestamp' => $this->timestamp,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'workplace_id' => $this->workplaceId,
        ];
    }
}
