<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Infrastructure\Event;

use App\DDD\Backoffice\ClockIn\Domain\Event\ClockInCreatedEvent;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Workplace\Domain\Interface\WorkplaceRepositoryInterface;
use App\DDD\Shared\Domain\Event\DomainEvent;

/**
 * Enricher específico para eventos de ClockIn
 * Añade información del empleado y workplace
 */
final class ClockInEventEnricher
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $employeeRepository,
        private readonly WorkplaceRepositoryInterface $workplaceRepository,
    ) {}

    /**
     * Enriquece eventos de ClockIn con datos relacionados
     */
    public function __invoke(DomainEvent $event, array $currentMetadata): ?array
    {
        if (!$event instanceof ClockInCreatedEvent) {
            return null;
        }

        return [
            'employee' => $this->getEmployeeInfo($event->employeeId()),
            'workplace' => $this->getWorkplaceInfo($event->workplaceId()),
            'clock_in' => $this->getClockInContext($event),
        ];
    }

    private function getEmployeeInfo(int $employeeId): array
    {
        try {
            $employee = $this->employeeRepository->findById($employeeId);

            if ($employee === null) {
                return ['id' => $employeeId, 'found' => false];
            }

            return [
                'id' => $employeeId,
                'found' => true,
                'name' => $employee->name()->value(),
                'last_name' => $employee->lastName()->value(),
                'full_name' => $employee->fullName(),
                'email' => $employee->email()->value(),
                'code' => $employee->code()?->value(),
                'is_active' => $employee->isActive()->value(),
                'has_planification' => $employee->hasPlanification(),
                'workplaces' => $employee->workplaceIds(),
            ];
        } catch (\Throwable $e) {
            return [
                'id' => $employeeId,
                'found' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getWorkplaceInfo(?int $workplaceId): ?array
    {
        if ($workplaceId === null) {
            return null;
        }

        try {
            $workplace = $this->workplaceRepository->findById($workplaceId);

            if ($workplace === null) {
                return ['id' => $workplaceId, 'found' => false];
            }

            return [
                'id' => $workplaceId,
                'found' => true,
                'name' => $workplace->name()->value(),
                'city' => $workplace->city()->value(),
                'has_geofence' => $workplace->hasGeofence(),
                'geofence_radius' => $workplace->radius()?->value(),
            ];
        } catch (\Throwable $e) {
            return [
                'id' => $workplaceId,
                'found' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getClockInContext(ClockInCreatedEvent $event): array
    {
        $timestamp = new \DateTimeImmutable($event->timestamp());

        return [
            'type' => $event->type(),
            'type_label' => $this->getTypeLabel($event->type()),
            'day_of_week' => (int) $timestamp->format('N'),
            'day_name' => $this->getDayName((int) $timestamp->format('N')),
            'hour' => (int) $timestamp->format('H'),
            'is_weekend' => in_array((int) $timestamp->format('N'), [6, 7]),
            'is_early_morning' => (int) $timestamp->format('H') < 7,
            'is_late_night' => (int) $timestamp->format('H') >= 22,
        ];
    }

    private function getTypeLabel(string $type): string
    {
        return match ($type) {
            'entry' => 'Entrada',
            'exit' => 'Salida',
            'break_start' => 'Inicio descanso',
            'break_end' => 'Fin descanso',
            default => $type,
        };
    }

    private function getDayName(int $dayNumber): string
    {
        return match ($dayNumber) {
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
            default => 'Desconocido',
        };
    }
}
