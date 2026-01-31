<?php

declare(strict_types=1);

namespace App\DDD\Shared\Domain\ValueObject;

abstract class FloatValueObject
{
    protected function __construct(
        protected readonly float $value,
    ) {}

    public static function create(float $value): static
    {
        return new static($value);
    }

    public function value(): float
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
