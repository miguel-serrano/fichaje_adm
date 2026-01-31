<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Infrastructure;

use App\DDD\Authorization\Infrastructure\LaravelServiceProvider as AuthServiceProvider;
use App\DDD\Backoffice\Workplace\Application\Create\CreateWorkplaceCommand;
use App\DDD\Backoffice\Workplace\Application\Create\CreateWorkplaceCommandHandler;
use App\DDD\Backoffice\Workplace\Application\Delete\DeleteWorkplaceCommand;
use App\DDD\Backoffice\Workplace\Application\Delete\DeleteWorkplaceCommandHandler;
use App\DDD\Backoffice\Workplace\Application\Find\FindWorkplaceQuery;
use App\DDD\Backoffice\Workplace\Application\Find\FindWorkplaceQueryHandler;
use App\DDD\Backoffice\Workplace\Application\ListAll\ListWorkplacesQuery;
use App\DDD\Backoffice\Workplace\Application\ListAll\ListWorkplacesQueryHandler;
use App\DDD\Backoffice\Workplace\Application\Update\UpdateWorkplaceCommand;
use App\DDD\Backoffice\Workplace\Application\Update\UpdateWorkplaceCommandHandler;
use App\DDD\Backoffice\Workplace\Domain\Interface\WorkplaceRepositoryInterface;
use App\DDD\Backoffice\Workplace\Domain\Service\GeofenceValidator;
use App\DDD\Backoffice\Workplace\Domain\Voter\WorkplaceVoter;
use App\DDD\Backoffice\Workplace\Infrastructure\Repository\WorkplaceRepository;
use App\DDD\Shared\Infrastructure\Laravel\AbstractLaravelServiceProvider;

final class LaravelServiceProvider extends AbstractLaravelServiceProvider
{
    public function register(): void
    {
        $this->getServiceContainer()->bind(
            WorkplaceRepositoryInterface::class,
            WorkplaceRepository::class,
        );

        // Servicio de validación de geofence
        $this->getServiceContainer()->bind(GeofenceValidator::class);

        // Registrar voter
        $this->getServiceContainer()->bind(WorkplaceVoter::class);
        AuthServiceProvider::tagVoter($this, WorkplaceVoter::class);
    }

    protected function mapQueries(): void
    {
        $this->getQueryBus()->map([
            ListWorkplacesQuery::class => ListWorkplacesQueryHandler::class,
            FindWorkplaceQuery::class => FindWorkplaceQueryHandler::class,
        ]);
    }

    protected function mapCommands(): void
    {
        $this->getCommandBus()->map([
            CreateWorkplaceCommand::class => CreateWorkplaceCommandHandler::class,
            UpdateWorkplaceCommand::class => UpdateWorkplaceCommandHandler::class,
            DeleteWorkplaceCommand::class => DeleteWorkplaceCommandHandler::class,
        ]);
    }
}
