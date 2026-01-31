<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Domain;

use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceId;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceName;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceAddress;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceCity;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplacePostalCode;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceLatitude;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceLongitude;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceRadius;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceIsActive;

class Workplace
{
    protected function __construct(
        private readonly WorkplaceId $id,
        private readonly WorkplaceName $name,
        private readonly ?WorkplaceAddress $address,
        private readonly ?WorkplaceCity $city,
        private readonly ?WorkplacePostalCode $postalCode,
        private readonly ?WorkplaceLatitude $latitude,
        private readonly ?WorkplaceLongitude $longitude,
        private readonly ?WorkplaceRadius $radius,
        private WorkplaceIsActive $isActive,
    ) {}

    public static function create(
        WorkplaceId $id,
        WorkplaceName $name,
        ?WorkplaceAddress $address = null,
        ?WorkplaceCity $city = null,
        ?WorkplacePostalCode $postalCode = null,
        ?WorkplaceLatitude $latitude = null,
        ?WorkplaceLongitude $longitude = null,
        ?WorkplaceRadius $radius = null,
    ): self {
        return new self(
            id: $id,
            name: $name,
            address: $address,
            city: $city,
            postalCode: $postalCode,
            latitude: $latitude,
            longitude: $longitude,
            radius: $radius,
            isActive: WorkplaceIsActive::active(),
        );
    }

    public static function fromPrimitives(array $data): self
    {
        return new self(
            id: WorkplaceId::create($data['id']),
            name: WorkplaceName::create($data['name']),
            address: isset($data['address']) ? WorkplaceAddress::create($data['address']) : null,
            city: isset($data['city']) ? WorkplaceCity::create($data['city']) : null,
            postalCode: isset($data['postal_code']) ? WorkplacePostalCode::create($data['postal_code']) : null,
            latitude: isset($data['latitude']) ? WorkplaceLatitude::create($data['latitude']) : null,
            longitude: isset($data['longitude']) ? WorkplaceLongitude::create($data['longitude']) : null,
            radius: isset($data['radius']) ? WorkplaceRadius::create($data['radius']) : null,
            isActive: WorkplaceIsActive::fromBool($data['is_active'] ?? true),
        );
    }

    public function id(): WorkplaceId
    {
        return $this->id;
    }

    public function name(): WorkplaceName
    {
        return $this->name;
    }

    public function address(): ?WorkplaceAddress
    {
        return $this->address;
    }

    public function city(): ?WorkplaceCity
    {
        return $this->city;
    }

    public function postalCode(): ?WorkplacePostalCode
    {
        return $this->postalCode;
    }

    public function latitude(): ?WorkplaceLatitude
    {
        return $this->latitude;
    }

    public function longitude(): ?WorkplaceLongitude
    {
        return $this->longitude;
    }

    public function radius(): ?WorkplaceRadius
    {
        return $this->radius;
    }

    public function isActive(): bool
    {
        return $this->isActive->value();
    }

    public function hasGeolocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function hasGeofence(): bool
    {
        return $this->hasGeolocation() && $this->radius !== null;
    }

    /**
     * Verifica si unas coordenadas están dentro del radio permitido
     */
    public function isWithinGeofence(float $latitude, float $longitude): bool
    {
        if (!$this->hasGeofence()) {
            return true; // Sin geofence, siempre válido
        }

        $distance = $this->calculateDistance($latitude, $longitude);

        return $distance <= $this->radius->value();
    }

    /**
     * Calcula la distancia en metros usando la fórmula de Haversine
     */
    public function calculateDistance(float $latitude, float $longitude): float
    {
        if (!$this->hasGeolocation()) {
            return 0.0;
        }

        $earthRadius = 6371000; // Radio de la Tierra en metros

        $latFrom = deg2rad($this->latitude->value());
        $lonFrom = deg2rad($this->longitude->value());
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) ** 2 +
             cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function activate(): void
    {
        $this->isActive = WorkplaceIsActive::active();
    }

    public function deactivate(): void
    {
        $this->isActive = WorkplaceIsActive::inactive();
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'address' => $this->address?->value(),
            'city' => $this->city?->value(),
            'postal_code' => $this->postalCode?->value(),
            'latitude' => $this->latitude?->value(),
            'longitude' => $this->longitude?->value(),
            'radius' => $this->radius?->value(),
            'is_active' => $this->isActive->value(),
        ];
    }
}
