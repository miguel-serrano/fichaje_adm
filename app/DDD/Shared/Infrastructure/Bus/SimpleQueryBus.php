<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Bus;

use Illuminate\Contracts\Container\Container;

final class SimpleQueryBus implements QueryBusInterface
{
    /**
     * @var array<class-string, class-string>
     */
    private array $handlers = [];

    public function __construct(
        private readonly Container $container,
    ) {}

    public function ask(object $query): mixed
    {
        $queryClass = get_class($query);

        if (!isset($this->handlers[$queryClass])) {
            throw new \RuntimeException(
                sprintf('No handler registered for query <%s>', $queryClass)
            );
        }

        $handler = $this->container->make($this->handlers[$queryClass]);

        return $handler($query);
    }

    public function map(array $map): void
    {
        $this->handlers = array_merge($this->handlers, $map);
    }
}
