<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Application\Subscriber;

use App\DDD\Backoffice\ClockIn\Domain\Event\GeofenceViolationDetectedEvent;
use App\DDD\Backoffice\Notification\Domain\Service\Notifier;
use App\DDD\Shared\Domain\Event\DomainEventSubscriber;

final class NotifyOnGeofenceViolation implements DomainEventSubscriber
{
    public function __construct(
        private readonly Notifier $notifier,
        private readonly ManagerFinderInterface $managerFinder,
    ) {}

    public static function subscribedTo(): array
    {
        return [
            GeofenceViolationDetectedEvent::eventName(),
        ];
    }

    public function __invoke(GeofenceViolationDetectedEvent $event): void
    {
        $managerId = $this->managerFinder->findManagerFor($event->employeeId());

        if ($managerId === null) {
            return;
        }

        $this->notifier->notifyGeofenceViolation(
            managerId: $managerId,
            employeeId: $event->employeeId(),
            employeeName: $event->employeeName(),
            workplaceId: $event->workplaceId(),
            workplaceName: $event->workplaceName(),
            distance: $event->distance(),
            allowedRadius: $event->allowedRadius(),
        );
    }
}
