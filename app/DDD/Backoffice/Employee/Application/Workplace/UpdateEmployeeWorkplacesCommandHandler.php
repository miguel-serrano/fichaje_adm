<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Workplace;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Employee\Domain\Employee;
use App\DDD\Backoffice\Employee\Domain\Exception\EmployeeNotFoundException;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Backoffice\Employee\Domain\Voter\EmployeeVoter;

final class UpdateEmployeeWorkplacesCommandHandler
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(UpdateEmployeeWorkplacesCommand $command): void
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

        $existing = $employee->toPrimitives();
        $existing['workplace_ids'] = $command->workplaceIds;

        $updated = Employee::fromPrimitives($existing);

        $this->repository->save($updated);
    }
}
