<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Application\Subscriber;

use App\DDD\Backoffice\ClockIn\Domain\Event\LateArrivalDetectedEvent;
use App\DDD\Backoffice\Notification\Domain\Service\Notifier;
use App\DDD\Shared\Domain\Event\DomainEventSubscriber;

final class NotifyOnLateArrival implements DomainEventSubscriber
{
    public function __construct(
        private readonly Notifier $notifier,
        private readonly ManagerFinderInterface $managerFinder,
    ) {}

    public static function subscribedTo(): array
    {
        return [
            LateArrivalDetectedEvent::eventName(),
        ];
    }

    public function __invoke(LateArrivalDetectedEvent $event): void
    {

        $managerId = $this->managerFinder->findManagerFor($event->employeeId());

        if ($managerId === null) {
            return;
        }

        $this->notifier->notifyLateArrival(
            managerId: $managerId,
            employeeId: $event->employeeId(),
            employeeName: $event->employeeName(),
            date: $event->date(),
            minutesLate: $event->minutesLate(),
        );
    }
}
