<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure;

use App\DDD\Authorization\Infrastructure\LaravelServiceProvider as AuthServiceProvider;
use App\DDD\Backoffice\Employee\Application\Contract\Delete\DeleteEmployeeContractCommand;
use App\DDD\Backoffice\Employee\Application\Contract\Delete\DeleteEmployeeContractCommandHandler;
use App\DDD\Backoffice\Employee\Application\Contract\Upsert\UpsertEmployeeContractCommand;
use App\DDD\Backoffice\Employee\Application\Contract\Upsert\UpsertEmployeeContractCommandHandler;
use App\DDD\Backoffice\Employee\Application\Create\CreateEmployeeCommand;
use App\DDD\Backoffice\Employee\Application\Create\CreateEmployeeCommandHandler;
use App\DDD\Backoffice\Employee\Application\Delete\DeleteEmployeeCommand;
use App\DDD\Backoffice\Employee\Application\Delete\DeleteEmployeeCommandHandler;
use App\DDD\Backoffice\Employee\Application\Planification\Delete\DeleteEmployeePlanificationCommand;
use App\DDD\Backoffice\Employee\Application\Planification\Delete\DeleteEmployeePlanificationCommandHandler;
use App\DDD\Backoffice\Employee\Application\Planification\Upsert\UpsertEmployeePlanificationCommand;
use App\DDD\Backoffice\Employee\Application\Planification\Upsert\UpsertEmployeePlanificationCommandHandler;
use App\DDD\Backoffice\Employee\Application\Find\FindEmployeeQuery;
use App\DDD\Backoffice\Employee\Application\Find\FindEmployeeQueryHandler;
use App\DDD\Backoffice\Employee\Application\ListAll\ListEmployeesQuery;
use App\DDD\Backoffice\Employee\Application\ListAll\ListEmployeesQueryHandler;
use App\DDD\Backoffice\Employee\Application\Update\UpdateEmployeeCommand;
use App\DDD\Backoffice\Employee\Application\Update\UpdateEmployeeCommandHandler;
use App\DDD\Backoffice\Employee\Application\Workplace\UpdateEmployeeWorkplacesCommand;
use App\DDD\Backoffice\Employee\Application\Workplace\UpdateEmployeeWorkplacesCommandHandler;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\Voter\EmployeeVoter;
use App\DDD\Backoffice\Employee\Infrastructure\Repository\EmployeeRepository;
use App\DDD\Shared\Infrastructure\Laravel\AbstractLaravelServiceProvider;

final class LaravelServiceProvider extends AbstractLaravelServiceProvider
{
    public function register(): void
    {
        $this->getServiceContainer()->bind(
            EmployeeRepositoryInterface::class,
            EmployeeRepository::class,
        );

        $this->getServiceContainer()->bind(EmployeeVoter::class);
        AuthServiceProvider::tagVoter($this, EmployeeVoter::class);
    }

    protected function mapQueries(): void
    {
        $this->getQueryBus()->map([
            ListEmployeesQuery::class => ListEmployeesQueryHandler::class,
            FindEmployeeQuery::class => FindEmployeeQueryHandler::class,
        ]);
    }

    protected function mapCommands(): void
    {
        $this->getCommandBus()->map([
            CreateEmployeeCommand::class => CreateEmployeeCommandHandler::class,
            UpdateEmployeeCommand::class => UpdateEmployeeCommandHandler::class,
            DeleteEmployeeCommand::class => DeleteEmployeeCommandHandler::class,
            UpsertEmployeeContractCommand::class => UpsertEmployeeContractCommandHandler::class,
            DeleteEmployeeContractCommand::class => DeleteEmployeeContractCommandHandler::class,
            UpsertEmployeePlanificationCommand::class => UpsertEmployeePlanificationCommandHandler::class,
            DeleteEmployeePlanificationCommand::class => DeleteEmployeePlanificationCommandHandler::class,
            UpdateEmployeeWorkplacesCommand::class => UpdateEmployeeWorkplacesCommandHandler::class,
        ]);
    }
}
