<?php

declare(strict_types=1);

namespace App\DDD\Shared\Domain\Criteria;

final class Order
{
    private function __construct(
        public readonly string $field,
        public readonly OrderDirection $direction,
    ) {}

    public static function create(string $field, OrderDirection $direction): self
    {
        return new self($field, $direction);
    }

    public static function asc(string $field): self
    {
        return new self($field, OrderDirection::ASC);
    }

    public static function desc(string $field): self
    {
        return new self($field, OrderDirection::DESC);
    }

    public static function none(): self
    {
        return new self('id', OrderDirection::ASC);
    }
}
