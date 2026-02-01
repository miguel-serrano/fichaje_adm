<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Event;

use App\DDD\Shared\Domain\Event\DomainEvent;
use App\DDD\Shared\Domain\Event\DomainEventSubscriber;
use App\DDD\Shared\Domain\Event\EventBusInterface;
use Illuminate\Contracts\Container\Container;

/**
 * EventBus que:
 * 1. Persiste TODOS los eventos en MySQL (Outbox pattern)
 * 2. Despacha a subscribers en memoria
 *
 * Los eventos se sincronizan a Elasticsearch via un job/comando separado
 */
final class OutboxEventBus implements EventBusInterface
{
    /** @var array<string, class-string<DomainEventSubscriber>[]> */
    private array $subscribers = [];

    public function __construct(
        private readonly Container $container,
        private readonly MysqlEventStore $mysqlEventStore,
    ) {}

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {

            $this->persistEvent($event);

            $this->dispatchToSubscribers($event);
        }
    }

    private function persistEvent(DomainEvent $event): void
    {
        try {
            $this->mysqlEventStore->append($event);
        } catch (\Throwable $e) {

            report($e);
            throw $e;
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
}
