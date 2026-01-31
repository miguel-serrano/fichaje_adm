<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Application\Update;

final class UpdateWorkplaceCommand
{
    public function __construct(
        public readonly int $activeUserId,
        public readonly int $workplaceId,
        public readonly string $name,
        public readonly ?string $address = null,
        public readonly ?string $city = null,
        public readonly ?string $postalCode = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
        public readonly ?int $radius = null,
        public readonly bool $isActive = true,
    ) {}
}
