<?php

return [
    App\Providers\AppServiceProvider::class,

    // DDD Service Providers
    App\DDD\Shared\Infrastructure\Laravel\SharedServiceProvider::class,
    App\DDD\Authorization\Infrastructure\LaravelServiceProvider::class,
    App\DDD\Backoffice\User\Infrastructure\LaravelServiceProvider::class,
    App\DDD\Backoffice\Employee\Infrastructure\LaravelServiceProvider::class,
    App\DDD\Backoffice\Workplace\Infrastructure\LaravelServiceProvider::class,
    App\DDD\Backoffice\ClockIn\Infrastructure\LaravelServiceProvider::class,
    App\DDD\Backoffice\Notification\Infrastructure\LaravelServiceProvider::class,
];
