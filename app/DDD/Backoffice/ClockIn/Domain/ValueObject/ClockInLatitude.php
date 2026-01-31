<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\ValueObject;

use App\DDD\Shared\Domain\ValueObject\FloatValueObject;

final class ClockInLatitude extends FloatValueObject
{
    public static function create(float $value): self
    {
        if ($value < -90 || $value > 90) {
            throw new \InvalidArgumentException('Latitude must be between -90 and 90');
        }

        return new self($value);
    }
}
