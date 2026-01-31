<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\Exception;

use App\DDD\Shared\Domain\Exception\DomainException;

final class InvalidEmployeeEmailException extends DomainException
{
    public static function empty(): self
    {
        return new self('El email del empleado no puede estar vacío');
    }

    public static function invalidFormat(string $email): self
    {
        return new self(
            sprintf('El formato del email "%s" no es válido', $email)
        );
    }
}
