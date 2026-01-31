<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\Exception;

use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInId;
use App\DDD\Shared\Domain\Exception\DomainException;

final class ClockInNotFoundException extends DomainException
{
    public static function withId(ClockInId $id): self
    {
        return new self(
            sprintf('ClockIn with id <%d> not found', $id->value())
        );
    }
}
