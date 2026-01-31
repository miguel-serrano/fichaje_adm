<?php

declare(strict_types=1);

namespace App\DDD\Authorization\Domain\Exception;

use App\DDD\Shared\Domain\Exception\DomainException;

final class AccessDeniedException extends DomainException
{
    public static function forAttribute(string $attribute, int $userId): self
    {
        return new self(
            sprintf('Access denied for user <%d> on attribute <%s>', $userId, $attribute)
        );
    }

    public static function withMessage(string $message): self
    {
        return new self($message);
    }
}
