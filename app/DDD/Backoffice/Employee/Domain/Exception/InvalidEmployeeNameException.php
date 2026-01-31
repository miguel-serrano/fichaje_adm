<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\Exception;

use App\DDD\Shared\Domain\Exception\DomainException;

final class InvalidEmployeeNameException extends DomainException
{
    public static function empty(string $field): self
    {
        return new self(
            sprintf('El %s del empleado no puede estar vacío', $field)
        );
    }

    public static function tooShort(string $field, int $minLength): self
    {
        return new self(
            sprintf('El %s del empleado debe tener al menos %d caracteres', $field, $minLength)
        );
    }

    public static function tooLong(string $field, int $maxLength): self
    {
        return new self(
            sprintf('El %s del empleado no puede tener más de %d caracteres', $field, $maxLength)
        );
    }
}
