<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Event;

use App\DDD\Shared\Domain\Event\DomainEvent;
use App\DDD\Shared\Domain\Event\DomainEventSubscriber;
use App\DDD\Shared\Domain\Event\EventBusInterface;
use App\DDD\Shared\Domain\Event\EventStoreInterface;
use Illuminate\Contracts\Container\Container;

/**
 * EventBus que:
 * 1. Persiste eventos en EventStore (Elasticsearch)
 * 2. Despacha a subscribers específicos
 */
final class PersistingEventBus implements EventBusInterface
{
    /** @var array<string, class-string<DomainEventSubscriber>[]> */
    private array $subscribers = [];

    public function __construct(
        private readonly Container $container,
        private readonly ?EventStoreInterface $eventStore = null,
    ) {}

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            // 1. Persistir en EventStore (si está configurado)
            $this->persistEvent($event);

            // 2. Despachar a subscribers
            $this->dispatchToSubscribers($event);
        }
    }

    private function persistEvent(DomainEvent $event): void
    {
        if ($this->eventStore !== null) {
            try {
                $this->eventStore->append($event);
            } catch (\Throwable $e) {
                // Log error pero no fallar el flujo principal
                report($e);
            }
        }
    }

    private function dispatchToSubscribers(DomainEvent $event): void
    {
        $eventName = $event::eventName();

        if (!isset($this->subscribers[$eventName])) {
            return;
        }

        foreach ($this->subscribers[$eventName] as $subscriberClass) {
            try {
                $subscriber = $this->container->make($subscriberClass);
                $subscriber($event);
            } catch (\Throwable $e) {
                // Log error pero continuar con otros subscribers
                report($e);
            }
        }
    }

    /**
     * @param class-string<DomainEventSubscriber> $subscriberClass
     */
    public function subscribe(string $subscriberClass): void
    {
        $eventNames = $subscriberClass::subscribedTo();

        foreach ($eventNames as $eventName) {
            $this->subscribers[$eventName][] = $subscriberClass;
        }
    }

    /**
     * Suscribe un subscriber a TODOS los eventos (útil para logging/auditoría)
     *
     * @param class-string<DomainEventSubscriber> $subscriberClass
     */
    public function subscribeToAll(string $subscriberClass): void
    {
        // Este subscriber se añadirá dinámicamente cuando se publique cualquier evento
        $this->subscribers['*'][] = $subscriberClass;
    }
}
