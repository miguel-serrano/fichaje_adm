<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Planification\Delete;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Employee\Domain\Exception\EmployeeNotFoundException;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Backoffice\Employee\Domain\Voter\EmployeeVoter;
use App\DDD\Backoffice\Employee\Infrastructure\Repository\EloquentEmployeePlanificationModel;

final class DeleteEmployeePlanificationCommandHandler
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(DeleteEmployeePlanificationCommand $command): void
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

        EloquentEmployeePlanificationModel::where('employee_id', $command->employeeId)->delete();
    }
}
