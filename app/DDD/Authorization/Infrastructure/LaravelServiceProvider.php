<?php

declare(strict_types=1);

namespace App\DDD\Authorization\Infrastructure;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Authorization\Domain\Interface\VoterInterface;
use App\DDD\Authorization\Infrastructure\Service\UserPermissionsChecker;
use App\DDD\Authorization\Infrastructure\Service\UserPermissionsCheckerInterface;
use App\DDD\Authorization\Infrastructure\Service\Voter\AuthorizationService;
use Illuminate\Support\ServiceProvider;

final class LaravelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserPermissionsCheckerInterface::class,
            UserPermissionsChecker::class,
        );

        $this->app->singleton(AuthorizationServiceInterface::class, function ($app) {
            $service = new AuthorizationService();

            foreach ($app->tagged('voters') as $voter) {
                $service->registerVoter($voter);
            }

            return $service;
        });
    }

    /**
     * Método helper para que otros ServiceProviders registren sus voters
     */
    public static function tagVoter(ServiceProvider $provider, string $voterClass): void
    {
        $provider->app->tag([$voterClass], 'voters');
    }
}
