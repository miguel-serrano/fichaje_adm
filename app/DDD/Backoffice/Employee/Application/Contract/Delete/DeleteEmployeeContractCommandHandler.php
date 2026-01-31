<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Contract\Delete;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Employee\Domain\Exception\EmployeeNotFoundException;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Backoffice\Employee\Domain\Voter\EmployeeVoter;
use App\DDD\Backoffice\Employee\Infrastructure\Repository\EloquentEmployeeContractModel;

final class DeleteEmployeeContractCommandHandler
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(DeleteEmployeeContractCommand $command): void
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

        EloquentEmployeeContractModel::where('employee_id', $command->employeeId)->delete();
    }
}
