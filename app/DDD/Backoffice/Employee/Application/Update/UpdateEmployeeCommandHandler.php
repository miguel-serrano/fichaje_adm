<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Update;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Employee\Domain\Employee;
use App\DDD\Backoffice\Employee\Domain\Exception\EmployeeNotFoundException;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Backoffice\Employee\Domain\Voter\EmployeeVoter;

final class UpdateEmployeeCommandHandler
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(UpdateEmployeeCommand $command): void
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

        $updated = Employee::fromPrimitives(array_merge($existing, [
            'id' => $command->employeeId,
            'name' => $command->name,
            'last_name' => $command->lastName,
            'email' => $command->email,
            'phone' => $command->phone,
            'nid' => $command->nid,
            'code' => $command->code,
            'is_active' => $command->isActive,
        ]));

        $this->repository->save($updated);
    }
}
