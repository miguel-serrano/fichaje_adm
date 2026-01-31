<?php

declare(strict_types=1);

namespace App\DDD\Shared\Domain\Event;

interface EventStoreInterface
{
    /**
     * Almacena uno o más eventos
     */
    public function append(DomainEvent ...$events): void;

    /**
     * Obtiene eventos por aggregate ID
     *
     * @return DomainEvent[]
     */
    public function getByAggregateId(string $aggregateId): array;

    /**
     * Obtiene eventos por tipo
     *
     * @return DomainEvent[]
     */
    public function getByEventName(string $eventName, int $limit = 100): array;

    /**
     * Obtiene eventos en un rango de fechas
     *
     * @return DomainEvent[]
     */
    public function getByDateRange(
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
        ?string $eventName = null,
        int $limit = 1000,
    ): array;

    /**
     * Busca eventos con filtros
     *
     * @return DomainEvent[]
     */
    public function search(array $filters, int $limit = 100, int $offset = 0): array;
}
