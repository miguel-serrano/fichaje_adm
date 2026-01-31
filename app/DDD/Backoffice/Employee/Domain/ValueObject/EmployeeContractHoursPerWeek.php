<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\ValueObject;

use App\DDD\Shared\Domain\ValueObject\FloatValueObject;

final class EmployeeContractHoursPerWeek extends FloatValueObject
{
    public static function create(float $value): static
    {
        if ($value < 0 || $value > 168) {
            throw new \InvalidArgumentException(
                sprintf('Hours per week must be between 0 and 168, got %f', $value)
            );
        }

        return new static($value);
    }

    public static function fullTime(): self
    {
        return new self(40.0);
    }

    public static function partTime(): self
    {
        return new self(20.0);
    }
}
