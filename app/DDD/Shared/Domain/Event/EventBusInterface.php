<?php

declare(strict_types=1);

namespace App\DDD\Shared\Domain\Event;

interface EventBusInterface
{
    /**
     * Publica uno o más eventos de dominio
     *
     * @param DomainEvent ...$events
     */
    public function publish(DomainEvent ...$events): void;
}
