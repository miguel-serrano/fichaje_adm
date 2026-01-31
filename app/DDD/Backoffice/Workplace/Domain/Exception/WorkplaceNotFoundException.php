<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Domain\Exception;

use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceId;
use App\DDD\Shared\Domain\Exception\DomainException;

final class WorkplaceNotFoundException extends DomainException
{
    public static function withId(WorkplaceId $id): self
    {
        return new self(
            sprintf('Workplace with id <%d> not found', $id->value())
        );
    }
}
