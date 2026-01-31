<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Bus;

use Illuminate\Support\Facades\DB;

/**
 * Decorator que envuelve la ejecución de commands en una transacción
 */
final class TransactionalCommandBus implements CommandBusInterface
{
    private array $handlers = [];

    public function __construct(
        private readonly CommandBusInterface $innerBus,
    ) {}

    public function dispatch(object $command): void
    {
        DB::transaction(function () use ($command) {
            $this->innerBus->dispatch($command);
        });
    }

    public function map(array $handlers): void
    {
        $this->innerBus->map($handlers);
    }
}
