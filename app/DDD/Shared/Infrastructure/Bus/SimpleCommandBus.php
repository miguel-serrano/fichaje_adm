<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Bus;

use Illuminate\Contracts\Container\Container;

final class SimpleCommandBus implements CommandBusInterface
{
    /**
     * @var array<class-string, class-string>
     */
    private array $handlers = [];

    public function __construct(
        private readonly Container $container,
    ) {}

    public function dispatch(object $command): void
    {
        $commandClass = get_class($command);

        if (!isset($this->handlers[$commandClass])) {
            throw new \RuntimeException(
                sprintf('No handler registered for command <%s>', $commandClass)
            );
        }

        $handler = $this->container->make($this->handlers[$commandClass]);
        $handler($command);
    }

    public function map(array $map): void
    {
        $this->handlers = array_merge($this->handlers, $map);
    }
}
