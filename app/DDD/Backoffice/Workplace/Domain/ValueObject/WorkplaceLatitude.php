<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Domain\ValueObject;

use App\DDD\Backoffice\Workplace\Domain\Exception\InvalidCoordinatesException;
use App\DDD\Shared\Domain\ValueObject\FloatValueObject;

final class WorkplaceLatitude extends FloatValueObject
{
    private const MIN = -90.0;
    private const MAX = 90.0;

    public static function create(float $value): static
    {
        if ($value < self::MIN || $value > self::MAX) {
            throw InvalidCoordinatesException::invalidLatitude($value);
        }

        return new static($value);
    }
}
