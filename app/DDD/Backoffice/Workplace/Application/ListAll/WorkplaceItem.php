<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Application\ListAll;

use App\DDD\Backoffice\Workplace\Domain\Workplace;

final class WorkplaceItem
{
    private function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $address,
        public readonly ?string $city,
        public readonly ?string $postalCode,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly ?int $radius,
        public readonly bool $isActive,
        public readonly bool $hasGeofence,
    ) {}

    public static function fromWorkplace(Workplace $workplace): self
    {
        return new self(
            id: $workplace->id()->value(),
            name: $workplace->name()->value(),
            address: $workplace->address()?->value(),
            city: $workplace->city()?->value(),
            postalCode: $workplace->postalCode()?->value(),
            latitude: $workplace->latitude()?->value(),
            longitude: $workplace->longitude()?->value(),
            radius: $workplace->radius()?->value(),
            isActive: $workplace->isActive(),
            hasGeofence: $workplace->hasGeofence(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postalCode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'radius' => $this->radius,
            'is_active' => $this->isActive,
            'has_geofence' => $this->hasGeofence,
        ];
    }
}
