<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Domain\Exception;

use App\DDD\Shared\Domain\Exception\DomainException;

final class GeofenceViolationException extends DomainException
{
    public static function outsideRadius(
        string $workplaceName,
        float $distance,
        int $allowedRadius,
    ): self {
        return new self(
            sprintf(
                'Clock-in location is %.0f meters away from workplace "%s". Maximum allowed: %d meters',
                $distance,
                $workplaceName,
                $allowedRadius,
            )
        );
    }
}
