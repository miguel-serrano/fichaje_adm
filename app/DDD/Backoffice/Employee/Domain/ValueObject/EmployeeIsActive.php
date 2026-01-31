<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\ValueObject;

final class EmployeeIsActive
{
    private function __construct(
        private readonly bool $value,
    ) {}

    public static function active(): self
    {
        return new self(true);
    }

    public static function inactive(): self
    {
        return new self(false);
    }

    public static function fromBool(bool $value): self
    {
        return new self($value);
    }

    public function value(): bool
    {
        return $this->value;
    }

    public function toggle(): self
    {
        return new self(!$this->value);
    }
}
