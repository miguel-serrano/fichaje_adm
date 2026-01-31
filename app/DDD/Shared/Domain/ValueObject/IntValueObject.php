<?php

declare(strict_types=1);

namespace App\DDD\Shared\Domain\ValueObject;

abstract class IntValueObject
{
    protected function __construct(
        protected readonly int $value,
    ) {}

    public static function create(int $value): static
    {
        return new static($value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
