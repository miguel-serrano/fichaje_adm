<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Find;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Employee\Domain\Exception\EmployeeNotFoundException;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Backoffice\Employee\Domain\Voter\EmployeeVoter;

final class FindEmployeeQueryHandler
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(FindEmployeeQuery $query): FindEmployeeResponse
    {
        $this->authorizationService->denyAccessUnlessGranted(
            attribute: EmployeeVoter::view(),
            userId: $query->activeUserId,
        );

        $employee = $this->repository->findById(EmployeeId::create($query->employeeId));

        if ($employee === null) {
            throw EmployeeNotFoundException::withId(EmployeeId::create($query->employeeId));
        }

        return FindEmployeeResponse::fromEmployee($employee);
    }
}
