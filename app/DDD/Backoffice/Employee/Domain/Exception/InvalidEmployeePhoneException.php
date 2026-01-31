<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\Exception;

use App\DDD\Shared\Domain\Exception\DomainException;

final class InvalidEmployeePhoneException extends DomainException
{
    public static function empty(): self
    {
        return new self('El teléfono del empleado no puede estar vacío');
    }

    public static function invalidFormat(string $phone): self
    {
        return new self(
            sprintf('El formato del teléfono "%s" no es válido', $phone)
        );
    }

    public static function invalidLength(string $phone, int $min, int $max): self
    {
        return new self(
            sprintf(
                'El teléfono "%s" debe tener entre %d y %d dígitos',
                $phone,
                $min,
                $max
            )
        );
    }
}
