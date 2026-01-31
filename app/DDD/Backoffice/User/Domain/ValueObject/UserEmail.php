<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\User\Domain\ValueObject;

use App\DDD\Shared\Domain\ValueObject\StringValueObject;

final class UserEmail extends StringValueObject
{
    public static function create(string $value): static
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid email format: %s', $value)
            );
        }

        return new static($value);
    }
}
