<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\ValueObject;

use App\DDD\Backoffice\Employee\Domain\Exception\InvalidEmployeeNameException;
use App\DDD\Shared\Domain\ValueObject\StringValueObject;

final class EmployeeName extends StringValueObject
{
    private const MIN_LENGTH = 2;
    private const MAX_LENGTH = 100;

    public static function create(string $value): static
    {
        $value = trim($value);

        if (empty($value)) {
            throw InvalidEmployeeNameException::empty('nombre');
        }

        $length = mb_strlen($value);

        if ($length < self::MIN_LENGTH) {
            throw InvalidEmployeeNameException::tooShort('nombre', self::MIN_LENGTH);
        }

        if ($length > self::MAX_LENGTH) {
            throw InvalidEmployeeNameException::tooLong('nombre', self::MAX_LENGTH);
        }

        return new static($value);
    }
}
