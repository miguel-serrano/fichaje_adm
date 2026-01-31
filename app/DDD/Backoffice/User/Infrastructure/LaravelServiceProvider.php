<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\User\Infrastructure;

use App\DDD\Authorization\Domain\Interface\UserRepositoryInterface as AuthUserRepositoryInterface;
use App\DDD\Backoffice\User\Domain\Interface\UserRepositoryInterface;
use App\DDD\Backoffice\User\Infrastructure\Repository\UserRepository;
use App\DDD\Backoffice\User\Infrastructure\Service\AuthorizationUserRepository;
use App\DDD\Shared\Infrastructure\Laravel\AbstractLaravelServiceProvider;

final class LaravelServiceProvider extends AbstractLaravelServiceProvider
{
    public function register(): void
    {
        // Repositorio de User
        $this->getServiceContainer()->bind(
            UserRepositoryInterface::class,
            UserRepository::class,
        );

        // Adaptador para el sistema de Authorization
        $this->getServiceContainer()->bind(
            AuthUserRepositoryInterface::class,
            AuthorizationUserRepository::class,
        );
    }
}
