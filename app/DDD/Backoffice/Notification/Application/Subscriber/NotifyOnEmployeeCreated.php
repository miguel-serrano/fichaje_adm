<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Application\Subscriber;

use App\DDD\Backoffice\Employee\Domain\Event\EmployeeCreatedEvent;
use App\DDD\Backoffice\Notification\Domain\Service\Notifier;
use App\DDD\Backoffice\Notification\Domain\ValueObject\NotificationType;
use App\DDD\Shared\Domain\Event\DomainEventSubscriber;

/**
 * Notifica a los administradores cuando se crea un nuevo empleado
 */
final class NotifyOnEmployeeCreated implements DomainEventSubscriber
{
    public function __construct(
        private readonly Notifier $notifier,
        private readonly ManagerFinderInterface $managerFinder,
    ) {}

    public static function subscribedTo(): array
    {
        return [
            EmployeeCreatedEvent::eventName(),
        ];
    }

    public function __invoke(EmployeeCreatedEvent $event): void
    {
        $admins = $this->managerFinder->findAllAdmins();

        foreach ($admins as $adminId) {
            $this->notifier->notify(
                type: NotificationType::EMPLOYEE_CREATED,
                title: 'Nuevo empleado registrado',
                body: sprintf(
                    'Se ha registrado un nuevo empleado: %s %s (%s)',
                    $event->name(),
                    $event->lastName(),
                    $event->email(),
                ),
                recipientId: $adminId,
                data: [
                    'employee_id' => (int) $event->aggregateId(),
                    'employee_name' => $event->name() . ' ' . $event->lastName(),
                    'employee_email' => $event->email(),
                ],
            );
        }
    }
}
