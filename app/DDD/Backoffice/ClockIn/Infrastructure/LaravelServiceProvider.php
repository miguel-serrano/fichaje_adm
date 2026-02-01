<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Infrastructure;

use App\DDD\Authorization\Infrastructure\LaravelServiceProvider as AuthServiceProvider;
use App\DDD\Backoffice\ClockIn\Application\Create\CreateClockInCommand;
use App\DDD\Backoffice\ClockIn\Application\Create\CreateClockInCommandHandler;
use App\DDD\Backoffice\ClockIn\Application\GetWorkedHoursReport\GetWorkedHoursReportQuery;
use App\DDD\Backoffice\ClockIn\Application\GetWorkedHoursReport\GetWorkedHoursReportQueryHandler;
use App\DDD\Backoffice\ClockIn\Application\ListByEmployee\ListClockInsByEmployeeQuery;
use App\DDD\Backoffice\ClockIn\Application\ListByEmployee\ListClockInsByEmployeeQueryHandler;
use App\DDD\Backoffice\ClockIn\Application\Subscriber\CheckLateArrivalOnClockInCreated;
use App\DDD\Backoffice\ClockIn\Domain\Interface\ClockInRepositoryInterface;
use App\DDD\Backoffice\ClockIn\Domain\Service\WorkedHoursCalculator;
use App\DDD\Backoffice\ClockIn\Domain\Voter\ClockInVoter;
use App\DDD\Backoffice\ClockIn\Infrastructure\Repository\ClockInRepository;
use App\DDD\Shared\Domain\Event\EventBusInterface;
use App\DDD\Shared\Infrastructure\Event\LaravelEventBus;
use App\DDD\Shared\Infrastructure\Laravel\AbstractLaravelServiceProvider;

final class LaravelServiceProvider extends AbstractLaravelServiceProvider
{
    public function register(): void
    {
        $this->getServiceContainer()->bind(
            ClockInRepositoryInterface::class,
            ClockInRepository::class,
        );

        $this->getServiceContainer()->bind(WorkedHoursCalculator::class);

        $this->getServiceContainer()->bind(ClockInVoter::class);
        AuthServiceProvider::tagVoter($this, ClockInVoter::class);
    }

    public function boot(): void
    {
        parent::boot();

        $this->registerEventSubscribers();
    }

    protected function mapQueries(): void
    {
        $this->getQueryBus()->map([
            ListClockInsByEmployeeQuery::class => ListClockInsByEmployeeQueryHandler::class,
            GetWorkedHoursReportQuery::class => GetWorkedHoursReportQueryHandler::class,
        ]);
    }

    protected function mapCommands(): void
    {
        $this->getCommandBus()->map([
            CreateClockInCommand::class => CreateClockInCommandHandler::class,
        ]);
    }

    private function registerEventSubscribers(): void
    {
        /** @var LaravelEventBus $eventBus */
        $eventBus = $this->getServiceContainer()->make(EventBusInterface::class);

        $eventBus->subscribe(CheckLateArrivalOnClockInCreated::class);
    }
}
