<?php

declare(strict_types=1);

namespace App\DDD\Shared\Domain\Criteria;

/**
 * Criteria para encapsular parámetros de búsqueda en repositorios
 */
final class Criteria
{
    /**
     * @param Filter[] $filters
     * @param Order|null $order
     */
    private function __construct(
        private readonly array $filters,
        private readonly ?Order $order,
        private readonly ?int $limit,
        private readonly ?int $offset,
    ) {}

    public static function create(
        array $filters = [],
        ?Order $order = null,
        ?int $limit = null,
        ?int $offset = null,
    ): self {
        return new self($filters, $order, $limit, $offset);
    }

    public static function empty(): self
    {
        return new self([], null, null, null);
    }

    public function hasFilters(): bool
    {
        return !empty($this->filters);
    }

    /**
     * @return Filter[]
     */
    public function filters(): array
    {
        return $this->filters;
    }

    public function order(): ?Order
    {
        return $this->order;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }

    public function withFilter(Filter $filter): self
    {
        return new self(
            [...$this->filters, $filter],
            $this->order,
            $this->limit,
            $this->offset,
        );
    }

    public function withOrder(Order $order): self
    {
        return new self(
            $this->filters,
            $order,
            $this->limit,
            $this->offset,
        );
    }

    public function withLimit(int $limit): self
    {
        return new self(
            $this->filters,
            $this->order,
            $limit,
            $this->offset,
        );
    }

    public function withOffset(int $offset): self
    {
        return new self(
            $this->filters,
            $this->order,
            $this->limit,
            $offset,
        );
    }
}
