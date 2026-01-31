<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\ListAll;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Employee\Domain\Employee;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\Voter\EmployeeVoter;

final class ListEmployeesQueryHandler
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
    ) {}

    public function __invoke(ListEmployeesQuery $query): ListEmployeesResponse
    {
        $this->authorizationService->denyAccessUnlessGranted(
            attribute: EmployeeVoter::view(),
            userId: $query->activeUserId,
        );

        $employees = $query->onlyActive
            ? $this->repository->findActive()
            : $this->repository->findAll();

        $items = array_map(
            fn (Employee $employee) => EmployeeItem::fromEmployee($employee),
            $employees,
        );

        return new ListEmployeesResponse($items);
    }
}
