<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Domain\Exception;

use App\DDD\Shared\Domain\Exception\DomainException;

final class InvalidWorkplaceRadiusException extends DomainException
{
    public static function tooSmall(int $value, int $min): self
    {
        return new self(
            sprintf('El radio %dm es demasiado pequeño. Mínimo: %dm', $value, $min)
        );
    }

    public static function tooLarge(int $value, int $max): self
    {
        return new self(
            sprintf('El radio %dm es demasiado grande. Máximo: %dm', $value, $max)
        );
    }
}
