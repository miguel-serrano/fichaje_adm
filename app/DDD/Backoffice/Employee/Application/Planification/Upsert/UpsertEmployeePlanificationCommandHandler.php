<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Planification\Upsert;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Employee\Domain\Employee;
use App\DDD\Backoffice\Employee\Domain\Exception\EmployeeNotFoundException;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Backoffice\Employee\Domain\ValueObject\WeekSchedule;
use App\DDD\Backoffice\Employee\Domain\Voter\EmployeeVoter;

final class UpsertEmployeePlanificationCommandHandler
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(UpsertEmployeePlanificationCommand $command): void
    {
        $this->authorizationService->denyAccessUnlessGranted(
            attribute: EmployeeVoter::edit(),
            userId: $command->activeUserId,
        );

        $employeeId = EmployeeId::create($command->employeeId);
        $employee = $this->repository->findById($employeeId);

        if ($employee === null) {
            throw EmployeeNotFoundException::withId($employeeId);
        }

        $weekSchedule = WeekSchedule::fromPrimitives($command->weekSchedule);

        $primitives = $employee->toPrimitives();

        $primitives['planification'] = [
            'id' => $employee->planification()?->id()->value() ?? $command->employeeId,
            'week_schedule' => $weekSchedule->toPrimitives(),
            'total_week_hours' => $weekSchedule->totalHours(),
        ];

        $updated = Employee::fromPrimitives($primitives);

        $this->repository->save($updated);
    }
}
