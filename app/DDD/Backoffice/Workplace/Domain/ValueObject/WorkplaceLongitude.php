<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Domain\ValueObject;

use App\DDD\Backoffice\Workplace\Domain\Exception\InvalidCoordinatesException;
use App\DDD\Shared\Domain\ValueObject\FloatValueObject;

final class WorkplaceLongitude extends FloatValueObject
{
    private const MIN = -180.0;
    private const MAX = 180.0;

    public static function create(float $value): static
    {
        if ($value < self::MIN || $value > self::MAX) {
            throw InvalidCoordinatesException::invalidLongitude($value);
        }

        return new static($value);
    }
}
