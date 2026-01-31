<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Bus;

interface QueryBusInterface
{
    public function ask(object $query): mixed;

    /**
     * @param array<class-string, class-string> $map
     */
    public function map(array $map): void;
}
