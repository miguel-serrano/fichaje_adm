<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Application\Create;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\ClockIn\Domain\ClockIn;
use App\DDD\Backoffice\ClockIn\Domain\Event\GeofenceViolationDetectedEvent;
use App\DDD\Backoffice\ClockIn\Domain\Interface\ClockInRepositoryInterface;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInLatitude;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInLongitude;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInNotes;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInTimestamp;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInType;
use App\DDD\Backoffice\ClockIn\Domain\Voter\ClockInVoter;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Backoffice\Workplace\Domain\Interface\WorkplaceRepositoryInterface;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceId;
use App\DDD\Shared\Domain\Event\EventBusInterface;

final class CreateClockInCommandHandler
{
    public function __construct(
        private readonly ClockInRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
        private readonly EventBusInterface $eventBus,
        private readonly EmployeeRepositoryInterface $employeeRepository,
        private readonly WorkplaceRepositoryInterface $workplaceRepository,
    ) {}

    public function __invoke(CreateClockInCommand $command): void
    {
        $this->authorizationService->denyAccessUnlessGranted(
            attribute: ClockInVoter::create(),
            userId: $command->activeUserId,
        );

        $clockIn = ClockIn::create(
            id: $this->repository->nextId(),
            employeeId: EmployeeId::create($command->employeeId),
            type: ClockInType::from($command->type),
            timestamp: ClockInTimestamp::fromString($command->timestamp),
            latitude: ClockInLatitude::create($command->latitude),
            longitude: ClockInLongitude::create($command->longitude),
            workplaceId: $command->workplaceId !== null
                ? WorkplaceId::create($command->workplaceId)
                : null,
            notes: $command->notes !== null
                ? ClockInNotes::create($command->notes)
                : null,
        );

        $this->repository->save($clockIn);

        $events = $clockIn->pullDomainEvents();

        if ($command->workplaceId !== null && ($command->latitude != 0.0 || $command->longitude != 0.0)) {
            $workplaceId = WorkplaceId::create($command->workplaceId);
            $workplace = $this->workplaceRepository->findById($workplaceId);

            if ($workplace !== null && $workplace->hasGeofence()) {
                if (!$workplace->isWithinGeofence($command->latitude, $command->longitude)) {
                    $distance = $workplace->calculateDistance($command->latitude, $command->longitude);

                    $employee = $this->employeeRepository->findById(EmployeeId::create($command->employeeId));
                    $employeeName = $employee?->fullName() ?? 'Desconocido';

                    $events[] = new GeofenceViolationDetectedEvent(
                        aggregateId: (string) $clockIn->id()->value(),
                        employeeId: $command->employeeId,
                        employeeName: $employeeName,
                        workplaceId: $command->workplaceId,
                        workplaceName: $workplace->name()->value(),
                        distance: $distance,
                        allowedRadius: $workplace->radius()->value(),
                    );
                }
            }
        }

        $this->eventBus->publish(...$events);
    }
}
