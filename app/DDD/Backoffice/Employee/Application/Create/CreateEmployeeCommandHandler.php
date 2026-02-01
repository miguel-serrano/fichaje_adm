<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Create;

use App\DDD\Authorization\Application\Service\AuthorizationServiceInterface;
use App\DDD\Backoffice\Employee\Domain\Employee;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeCode;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeEmail;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeLastName;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeName;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeNid;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeePhone;
use App\DDD\Backoffice\Employee\Domain\Voter\EmployeeVoter;
use App\DDD\Shared\Domain\Event\EventBusInterface;

final class CreateEmployeeCommandHandler
{
    public function __construct(
        private readonly EmployeeRepositoryInterface $repository,
        private readonly AuthorizationServiceInterface $authorizationService,
        private readonly EventBusInterface $eventBus,
    ) {}

    public function __invoke(CreateEmployeeCommand $command): void
    {
        $this->authorizationService->denyAccessUnlessGranted(
            attribute: EmployeeVoter::create(),
            userId: $command->activeUserId,
        );

        $employee = Employee::create(
            id: $this->repository->nextId(),
            name: EmployeeName::create($command->name),
            lastName: EmployeeLastName::create($command->lastName),
            email: EmployeeEmail::create($command->email),
            phone: $command->phone !== null ? EmployeePhone::create($command->phone) : null,
            nid: $command->nid !== null ? EmployeeNid::create($command->nid) : null,
            code: $command->code !== null ? EmployeeCode::create($command->code) : null,
        );

        $this->repository->save($employee);

        $this->eventBus->publish(...$employee->pullDomainEvents());
    }
}
