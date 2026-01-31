<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Laravel;

use App\DDD\Shared\Infrastructure\Bus\CommandBusInterface;
use App\DDD\Shared\Infrastructure\Bus\QueryBusInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

abstract class AbstractLaravelServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mapCommands();
        $this->mapQueries();
    }

    protected function getServiceContainer(): Container
    {
        return $this->app;
    }

    protected function getCommandBus(): CommandBusInterface
    {
        return $this->app->make(CommandBusInterface::class);
    }

    protected function getQueryBus(): QueryBusInterface
    {
        return $this->app->make(QueryBusInterface::class);
    }

    protected function mapCommands(): void
    {
        // Override en subclases
    }

    protected function mapQueries(): void
    {
        // Override en subclases
    }
}
