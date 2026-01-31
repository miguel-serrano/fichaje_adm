<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\Exception;

use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Shared\Domain\Exception\DomainException;

final class EmployeeNotFoundException extends DomainException
{
    public static function withId(EmployeeId $id): self
    {
        return new self(
            sprintf('Employee with id <%d> not found', $id->value())
        );
    }
}
