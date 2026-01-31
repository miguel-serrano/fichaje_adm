<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Delete;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Employee\Domain\Exception\EmployeeNotFoundException;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Backoffice\Employee\Domain\Voter\EmployeeVoter;

final class DeleteEmployeeCommandHandler
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(DeleteEmployeeCommand $command): void
    {
        $this->authorizationService->denyAccessUnlessGranted(
            attribute: EmployeeVoter::delete(),
            userId: $command->activeUserId,
        );

        $employeeId = EmployeeId::create($command->employeeId);
        $employee = $this->repository->findById($employeeId);

        if ($employee === null) {
            throw EmployeeNotFoundException::withId($employeeId);
        }

        $this->repository->delete($employee);
    }
}
