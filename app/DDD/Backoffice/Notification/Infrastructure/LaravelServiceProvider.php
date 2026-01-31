<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Infrastructure;

use App\DDD\Backoffice\Notification\Application\ListByRecipient\ListNotificationsQuery;
use App\DDD\Backoffice\Notification\Application\ListByRecipient\ListNotificationsQueryHandler;
use App\DDD\Backoffice\Notification\Application\MarkAllAsRead\MarkAllAsReadCommand;
use App\DDD\Backoffice\Notification\Application\MarkAllAsRead\MarkAllAsReadCommandHandler;
use App\DDD\Backoffice\Notification\Application\MarkAsRead\MarkAsReadCommand;
use App\DDD\Backoffice\Notification\Application\MarkAsRead\MarkAsReadCommandHandler;
use App\DDD\Backoffice\Notification\Application\Subscriber\ManagerFinderInterface;
use App\DDD\Backoffice\Notification\Application\Subscriber\NotifyOnEmployeeCreated;
use App\DDD\Backoffice\Notification\Application\Subscriber\NotifyOnGeofenceViolation;
use App\DDD\Backoffice\Notification\Application\Subscriber\NotifyOnLateArrival;
use App\DDD\Backoffice\Notification\Domain\Interface\NotificationRepositoryInterface;
use App\DDD\Backoffice\Notification\Domain\Service\Notifier;
use App\DDD\Backoffice\Notification\Infrastructure\Repository\NotificationRepository;
use App\DDD\Backoffice\Notification\Infrastructure\Service\SimpleManagerFinder;
use App\DDD\Shared\Domain\Event\EventBusInterface;
use App\DDD\Shared\Infrastructure\Event\LaravelEventBus;
use App\DDD\Shared\Infrastructure\Laravel\AbstractLaravelServiceProvider;

final class LaravelServiceProvider extends AbstractLaravelServiceProvider
{
    public function register(): void
    {
        $this->getServiceContainer()->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class,
        );

        $this->getServiceContainer()->bind(
            ManagerFinderInterface::class,
            SimpleManagerFinder::class,
        );

        // Servicio de notificaciones
        $this->getServiceContainer()->bind(Notifier::class);
    }

    public function boot(): void
    {
        parent::boot();

        // Registrar subscribers
        $this->registerEventSubscribers();
    }

    protected function mapQueries(): void
    {
        $this->getQueryBus()->map([
            ListNotificationsQuery::class => ListNotificationsQueryHandler::class,
        ]);
    }

    protected function mapCommands(): void
    {
        $this->getCommandBus()->map([
            MarkAsReadCommand::class => MarkAsReadCommandHandler::class,
            MarkAllAsReadCommand::class => MarkAllAsReadCommandHandler::class,
        ]);
    }

    private function registerEventSubscribers(): void
    {
        /** @var LaravelEventBus $eventBus */
        $eventBus = $this->getServiceContainer()->make(EventBusInterface::class);

        $eventBus->subscribe(NotifyOnLateArrival::class);
        $eventBus->subscribe(NotifyOnGeofenceViolation::class);
        $eventBus->subscribe(NotifyOnEmployeeCreated::class);
    }
}
