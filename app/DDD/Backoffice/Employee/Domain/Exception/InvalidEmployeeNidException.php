<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\Exception;

use App\DDD\Shared\Domain\Exception\DomainException;

final class InvalidEmployeeNidException extends DomainException
{
    public static function empty(): self
    {
        return new self('El NIF/NIE del empleado no puede estar vacío');
    }

    public static function invalidFormat(string $nid): self
    {
        return new self(
            sprintf('El NIF/NIE "%s" no tiene un formato válido', $nid)
        );
    }
}
