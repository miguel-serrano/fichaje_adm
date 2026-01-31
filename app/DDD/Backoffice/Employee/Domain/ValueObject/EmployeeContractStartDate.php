<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\ValueObject;

use DateTimeImmutable;

final class EmployeeContractStartDate
{
    private function __construct(
        private readonly DateTimeImmutable $value,
    ) {}

    public static function create(DateTimeImmutable $value): self
    {
        return new self($value);
    }

    public static function fromString(string $date): self
    {
        return new self(new DateTimeImmutable($date));
    }

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value->format('Y-m-d');
    }
}
