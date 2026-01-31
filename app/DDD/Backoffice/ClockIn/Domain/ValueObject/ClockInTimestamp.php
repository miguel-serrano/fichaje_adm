<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\ValueObject;

use DateTimeImmutable;
use DateTimeInterface;

final class ClockInTimestamp
{
    private function __construct(
        private readonly DateTimeImmutable $value,
    ) {}

    public static function create(DateTimeImmutable $value): self
    {
        return new self($value);
    }

    public static function now(): self
    {
        return new self(new DateTimeImmutable());
    }

    public static function fromString(string $datetime): self
    {
        return new self(new DateTimeImmutable($datetime));
    }

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value->format(DateTimeInterface::ATOM);
    }

    public function toDateString(): string
    {
        return $this->value->format('Y-m-d');
    }

    public function toTimeString(): string
    {
        return $this->value->format('H:i:s');
    }

    public function isBefore(self $other): bool
    {
        return $this->value < $other->value;
    }

    public function isAfter(self $other): bool
    {
        return $this->value > $other->value;
    }

    public function isSameDay(self $other): bool
    {
        return $this->toDateString() === $other->toDateString();
    }
}
