<?php

declare(strict_types=1);

namespace App\DDD\Shared\Domain\Event;

interface DomainEventSubscriber
{
    /**
     * Retorna los nombres de eventos a los que se suscribe
     *
     * @return string[] Array de event names
     */
    public static function subscribedTo(): array;
}
