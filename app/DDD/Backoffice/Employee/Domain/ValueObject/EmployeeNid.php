<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\ValueObject;

use App\DDD\Backoffice\Employee\Domain\Exception\InvalidEmployeeNidException;
use App\DDD\Shared\Domain\ValueObject\StringValueObject;

final class EmployeeNid extends StringValueObject
{
    private const DNI_REGEX = '/^[0-9]{8}[A-Z]$/';
    private const NIE_REGEX = '/^[XYZ][0-9]{7}[A-Z]$/';
    private const LETTERS = 'TRWAGMYFPDXBNJZSQVHLCKE';

    public static function create(string $value): static
    {
        $normalized = strtoupper(trim(str_replace(['-', ' ', '.'], '', $value)));

        if (empty($normalized)) {
            throw InvalidEmployeeNidException::empty();
        }

        if (!self::isValidDni($normalized) && !self::isValidNie($normalized)) {
            throw InvalidEmployeeNidException::invalidFormat($value);
        }

        return new static($normalized);
    }

    private static function isValidDni(string $dni): bool
    {
        if (!preg_match(self::DNI_REGEX, $dni)) {
            return false;
        }

        $number = (int) substr($dni, 0, 8);
        $letter = substr($dni, -1);

        return self::LETTERS[$number % 23] === $letter;
    }

    private static function isValidNie(string $nie): bool
    {
        if (!preg_match(self::NIE_REGEX, $nie)) {
            return false;
        }

        // Convertir primera letra a número
        $firstLetter = substr($nie, 0, 1);
        $replacement = match ($firstLetter) {
            'X' => '0',
            'Y' => '1',
            'Z' => '2',
            default => '',
        };

        $number = (int) ($replacement . substr($nie, 1, 7));
        $letter = substr($nie, -1);

        return self::LETTERS[$number % 23] === $letter;
    }
}
