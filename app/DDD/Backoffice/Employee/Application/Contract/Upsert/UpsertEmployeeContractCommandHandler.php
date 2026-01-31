<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Contract\Upsert;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Employee\Domain\Employee;
use App\DDD\Backoffice\Employee\Domain\Exception\EmployeeNotFoundException;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Backoffice\Employee\Domain\Voter\EmployeeVoter;

final class UpsertEmployeeContractCommandHandler
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(UpsertEmployeeContractCommand $command): void
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

        $primitives = $employee->toPrimitives();

        $primitives['contract'] = [
            'id' => $employee->contract()?->id()->value() ?? $command->employeeId,
            'type' => $command->type,
            'start_date' => $command->startDate,
            'end_date' => $command->endDate,
            'hours_per_week' => $command->hoursPerWeek,
        ];

        $updated = Employee::fromPrimitives($primitives);

        $this->repository->save($updated);
    }
}
