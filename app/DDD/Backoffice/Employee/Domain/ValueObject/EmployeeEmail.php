<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\ValueObject;

use App\DDD\Backoffice\Employee\Domain\Exception\InvalidEmployeeEmailException;
use App\DDD\Shared\Domain\ValueObject\StringValueObject;

final class EmployeeEmail extends StringValueObject
{
    public static function create(string $value): static
    {
        $value = trim(strtolower($value));

        if (empty($value)) {
            throw InvalidEmployeeEmailException::empty();
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw InvalidEmployeeEmailException::invalidFormat($value);
        }

        return new static($value);
    }
}
