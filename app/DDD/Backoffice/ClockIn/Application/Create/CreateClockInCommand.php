<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Application\Create;

final class CreateClockInCommand
{
    public function __construct(
        public readonly int $activeUserId,
        public readonly int $employeeId,
        public readonly string $type,
        public readonly string $timestamp,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly ?int $workplaceId = null,
        public readonly ?string $notes = null,
    ) {}
}
