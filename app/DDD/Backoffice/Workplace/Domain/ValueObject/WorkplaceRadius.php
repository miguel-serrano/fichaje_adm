<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Domain\ValueObject;

use App\DDD\Backoffice\Workplace\Domain\Exception\InvalidWorkplaceRadiusException;
use App\DDD\Shared\Domain\ValueObject\IntValueObject;

/**
 * Radio de geofence en metros
 */
final class WorkplaceRadius extends IntValueObject
{
    private const MIN_RADIUS = 10;
    private const MAX_RADIUS = 10000;
    private const DEFAULT_RADIUS = 100;

    public static function create(int $value): static
    {
        if ($value < self::MIN_RADIUS) {
            throw InvalidWorkplaceRadiusException::tooSmall($value, self::MIN_RADIUS);
        }

        if ($value > self::MAX_RADIUS) {
            throw InvalidWorkplaceRadiusException::tooLarge($value, self::MAX_RADIUS);
        }

        return new static($value);
    }

    /**
     * Radio por defecto: 100 metros
     */
    public static function default(): self
    {
        return new self(self::DEFAULT_RADIUS);
    }
}
