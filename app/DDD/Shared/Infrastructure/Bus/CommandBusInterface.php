<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Bus;

interface CommandBusInterface
{
    public function dispatch(object $command): void;

    /**
     * @param array<class-string, class-string> $map
     */
    public function map(array $map): void;
}
