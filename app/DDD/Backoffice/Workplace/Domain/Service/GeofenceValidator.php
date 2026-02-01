<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Domain\Service;

use App\DDD\Backoffice\Workplace\Domain\Exception\GeofenceViolationException;
use App\DDD\Backoffice\Workplace\Domain\Interface\WorkplaceRepositoryInterface;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceId;

/**
 * Servicio de dominio para validar que un fichaje
 * esté dentro del radio permitido del centro de trabajo
 */
final class GeofenceValidator
{
    public function __construct(
        private readonly WorkplaceRepositoryInterface $workplaceRepository,
    ) {}

    /**
     * Valida que las coordenadas estén dentro del geofence del workplace
     *
     * @throws GeofenceViolationException si está fuera del radio permitido
     */
    public function validate(
        WorkplaceId $workplaceId,
        float $latitude,
        float $longitude,
    ): void {
        $workplace = $this->workplaceRepository->findById($workplaceId);

        if ($workplace === null) {
            return;
        }

        if (!$workplace->hasGeofence()) {
            return;
        }

        if (!$workplace->isWithinGeofence($latitude, $longitude)) {
            $distance = $workplace->calculateDistance($latitude, $longitude);

            throw GeofenceViolationException::outsideRadius(
                workplaceName: $workplace->name()->value(),
                distance: $distance,
                allowedRadius: $workplace->radius()->value(),
            );
        }
    }

    /**
     * Verifica si las coordenadas están dentro del geofence sin lanzar excepción
     */
    public function isWithinGeofence(
        WorkplaceId $workplaceId,
        float $latitude,
        float $longitude,
    ): bool {
        $workplace = $this->workplaceRepository->findById($workplaceId);

        if ($workplace === null || !$workplace->hasGeofence()) {
            return true;
        }

        return $workplace->isWithinGeofence($latitude, $longitude);
    }

    /**
     * Calcula la distancia entre las coordenadas y el centro de trabajo
     */
    public function getDistanceToWorkplace(
        WorkplaceId $workplaceId,
        float $latitude,
        float $longitude,
    ): ?float {
        $workplace = $this->workplaceRepository->findById($workplaceId);

        if ($workplace === null || !$workplace->hasGeolocation()) {
            return null;
        }

        return $workplace->calculateDistance($latitude, $longitude);
    }
}
