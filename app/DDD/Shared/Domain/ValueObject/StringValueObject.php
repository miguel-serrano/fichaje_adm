<?php

declare(strict_types=1);

namespace App\DDD\Shared\Domain\ValueObject;

abstract class StringValueObject
{
    protected function __construct(
        protected readonly string $value,
    ) {}

    public static function create(string $value): static
    {
        return new static($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function isEmpty(): bool
    {
        return $this->value === '';
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
