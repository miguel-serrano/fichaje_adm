<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Application\Subscriber;

use App\DDD\Backoffice\ClockIn\Domain\Event\ClockInCreatedEvent;
use App\DDD\Backoffice\ClockIn\Domain\Event\LateArrivalDetectedEvent;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInType;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Shared\Domain\Event\DomainEventSubscriber;
use App\DDD\Shared\Domain\Event\EventBusInterface;
use DateTimeImmutable;

/**
 * Cuando se crea un fichaje de entrada, verifica si hay retraso
 * comparando con la planificación del empleado
 */
final class CheckLateArrivalOnClockInCreated implements DomainEventSubscriber
{
    private const TOLERANCE_MINUTES = 5; // Tolerancia de 5 minutos

    public function __construct(
        private readonly EmployeeRepositoryInterface $employeeRepository,
        private readonly EventBusInterface $eventBus,
    ) {}

    public static function subscribedTo(): array
    {
        return [
            ClockInCreatedEvent::eventName(),
        ];
    }

    public function __invoke(ClockInCreatedEvent $event): void
    {
        // Solo verificar fichajes de entrada
        if ($event->type() !== ClockInType::ENTRY->value) {
            return;
        }

        $employee = $this->employeeRepository->findById(
            EmployeeId::create($event->employeeId())
        );

        if ($employee === null || !$employee->hasPlanification()) {
            return;
        }

        $timestamp = new DateTimeImmutable($event->timestamp());
        $dayOfWeek = (int) $timestamp->format('N'); // 1=Monday, 7=Sunday

        $planification = $employee->planification();
        $daySchedule = $planification->weekSchedule()->getDay($dayOfWeek);

        if ($daySchedule === null || $daySchedule->isDayOff()) {
            return;
        }

        $expectedStartTime = $daySchedule->firstStartTime();
        if ($expectedStartTime === null) {
            return;
        }

        // Calcular hora esperada de entrada
        $expectedDateTime = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i',
            $timestamp->format('Y-m-d') . ' ' . $expectedStartTime
        );

        // Calcular diferencia en minutos
        $diffMinutes = ($timestamp->getTimestamp() - $expectedDateTime->getTimestamp()) / 60;

        // Si llegó tarde más allá de la tolerancia
        if ($diffMinutes > self::TOLERANCE_MINUTES) {
            $this->eventBus->publish(new LateArrivalDetectedEvent(
                aggregateId: $event->aggregateId(),
                employeeId: $event->employeeId(),
                employeeName: $employee->fullName(),
                date: $timestamp->format('Y-m-d'),
                minutesLate: (int) round($diffMinutes),
                expectedTime: $expectedStartTime,
                actualTime: $timestamp->format('H:i'),
            ));
        }
    }
}
