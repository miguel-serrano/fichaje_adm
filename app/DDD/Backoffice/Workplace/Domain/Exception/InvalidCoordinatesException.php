<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Domain\Exception;

use App\DDD\Shared\Domain\Exception\DomainException;

final class InvalidCoordinatesException extends DomainException
{
    public static function invalidLatitude(float $value): self
    {
        return new self(
            sprintf('La latitud %.6f no es válida. Debe estar entre -90 y 90', $value)
        );
    }

    public static function invalidLongitude(float $value): self
    {
        return new self(
            sprintf('La longitud %.6f no es válida. Debe estar entre -180 y 180', $value)
        );
    }
}
