<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Event;

use App\DDD\Shared\Domain\Event\DomainEvent;
use App\DDD\Shared\Domain\Event\EventStoreInterface;

/**
 * Subscriber que persiste TODOS los eventos en el EventStore (Elasticsearch)
 * 
 * Este subscriber debe registrarse para todos los eventos del sistema
 */
final class PersistDomainEventSubscriber
{
    public function __construct(
        private readonly EventStoreInterface $eventStore,
    ) {}

    public function __invoke(DomainEvent $event): void
    {
        $this->eventStore->append($event);
    }
}
