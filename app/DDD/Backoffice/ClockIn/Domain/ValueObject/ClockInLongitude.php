<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\ValueObject;

use App\DDD\Shared\Domain\ValueObject\FloatValueObject;

final class ClockInLongitude extends FloatValueObject
{
    public static function create(float $value): self
    {
        if ($value < -180 || $value > 180) {
            throw new \InvalidArgumentException('Longitude must be between -180 and 180');
        }

        return new self($value);
    }
}
