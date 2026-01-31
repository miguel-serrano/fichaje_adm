<?php

declare(strict_types=1);

namespace App\DDD\Shared\Domain\Criteria;

final class Filter
{
    private function __construct(
        public readonly string $field,
        public readonly FilterOperator $operator,
        public readonly mixed $value,
    ) {}

    public static function create(string $field, FilterOperator $operator, mixed $value): self
    {
        return new self($field, $operator, $value);
    }

    public static function equal(string $field, mixed $value): self
    {
        return new self($field, FilterOperator::EQUAL, $value);
    }

    public static function notEqual(string $field, mixed $value): self
    {
        return new self($field, FilterOperator::NOT_EQUAL, $value);
    }

    public static function greaterThan(string $field, mixed $value): self
    {
        return new self($field, FilterOperator::GREATER_THAN, $value);
    }

    public static function greaterThanOrEqual(string $field, mixed $value): self
    {
        return new self($field, FilterOperator::GREATER_THAN_OR_EQUAL, $value);
    }

    public static function lessThan(string $field, mixed $value): self
    {
        return new self($field, FilterOperator::LESS_THAN, $value);
    }

    public static function lessThanOrEqual(string $field, mixed $value): self
    {
        return new self($field, FilterOperator::LESS_THAN_OR_EQUAL, $value);
    }

    public static function like(string $field, string $value): self
    {
        return new self($field, FilterOperator::LIKE, $value);
    }

    public static function in(string $field, array $values): self
    {
        return new self($field, FilterOperator::IN, $values);
    }

    public static function notIn(string $field, array $values): self
    {
        return new self($field, FilterOperator::NOT_IN, $values);
    }

    public static function isNull(string $field): self
    {
        return new self($field, FilterOperator::IS_NULL, null);
    }

    public static function isNotNull(string $field): self
    {
        return new self($field, FilterOperator::IS_NOT_NULL, null);
    }

    public static function between(string $field, mixed $from, mixed $to): self
    {
        return new self($field, FilterOperator::BETWEEN, [$from, $to]);
    }
}
