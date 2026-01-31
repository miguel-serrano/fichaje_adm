<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\ValueObject;

use App\DDD\Backoffice\Employee\Domain\Exception\InvalidEmployeePhoneException;
use App\DDD\Shared\Domain\ValueObject\StringValueObject;

final class EmployeePhone extends StringValueObject
{
    private const MIN_LENGTH = 9;
    private const MAX_LENGTH = 15;

    public static function create(string $value): static
    {
        // Eliminar espacios y caracteres comunes
        $normalized = preg_replace('/[\s\-\(\)\.]+/', '', $value);

        if (empty($normalized)) {
            throw InvalidEmployeePhoneException::empty();
        }

        // Validar que solo contenga números y opcionalmente + al inicio
        if (!preg_match('/^\+?[0-9]+$/', $normalized)) {
            throw InvalidEmployeePhoneException::invalidFormat($value);
        }

        $length = strlen(ltrim($normalized, '+'));

        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            throw InvalidEmployeePhoneException::invalidLength($value, self::MIN_LENGTH, self::MAX_LENGTH);
        }

        return new static($normalized);
    }
}
