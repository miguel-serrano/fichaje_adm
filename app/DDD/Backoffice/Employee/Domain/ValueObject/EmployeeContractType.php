<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\ValueObject;

enum EmployeeContractType: string
{
    case INDEFINITE = 'indefinite';
    case TEMPORARY = 'temporary';
    case PART_TIME = 'part_time';
    case INTERNSHIP = 'internship';
    case FREELANCE = 'freelance';

    public function label(): string
    {
        return match ($this) {
            self::INDEFINITE => 'Indefinido',
            self::TEMPORARY => 'Temporal',
            self::PART_TIME => 'Tiempo parcial',
            self::INTERNSHIP => 'Prácticas',
            self::FREELANCE => 'Autónomo',
        };
    }
}
