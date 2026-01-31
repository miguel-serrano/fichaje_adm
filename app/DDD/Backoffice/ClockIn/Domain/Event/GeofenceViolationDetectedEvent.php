<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\Event;

use App\DDD\Shared\Domain\Event\AbstractDomainEvent;

final class GeofenceViolationDetectedEvent extends AbstractDomainEvent
{
    public function __construct(
        string $aggregateId,
        private readonly int $employeeId,
        private readonly string $employeeName,
        private readonly int $workplaceId,
        private readonly string $workplaceName,
        private readonly float $distance,
        private readonly int $allowedRadius,
    ) {
        parent::__construct($aggregateId);
    }

    public static function eventName(): string
    {
        return 'clock_in.geofence_violation_detected';
    }

    public function employeeId(): int
    {
        return $this->employeeId;
    }

    public function employeeName(): string
    {
        return $this->employeeName;
    }

    public function workplaceId(): int
    {
        return $this->workplaceId;
    }

    public function workplaceName(): string
    {
        return $this->workplaceName;
    }

    public function distance(): float
    {
        return $this->distance;
    }

    public function allowedRadius(): int
    {
        return $this->allowedRadius;
    }

    public function toPrimitives(): array
    {
        return [
            'aggregate_id' => $this->aggregateId(),
            'employee_id' => $this->employeeId,
            'employee_name' => $this->employeeName,
            'workplace_id' => $this->workplaceId,
            'workplace_name' => $this->workplaceName,
            'distance' => $this->distance,
            'allowed_radius' => $this->allowedRadius,
        ];
    }
}
