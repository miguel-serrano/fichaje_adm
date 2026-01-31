<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain;

use App\DDD\Backoffice\Employee\Domain\Entity\EmployeeContract;
use App\DDD\Backoffice\Employee\Domain\Entity\EmployeePlanification;
use App\DDD\Backoffice\Employee\Domain\Event\EmployeeCreatedEvent;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeName;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeLastName;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeEmail;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeePhone;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeNid;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeCode;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeIsActive;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceId;
use App\DDD\Shared\Domain\Event\RecordsDomainEvents;

class Employee
{
    use RecordsDomainEvents;

    /**
     * @param WorkplaceId[] $workplaceIds
     */
    protected function __construct(
        private readonly EmployeeId $id,
        private readonly EmployeeName $name,
        private readonly EmployeeLastName $lastName,
        private readonly EmployeeEmail $email,
        private readonly ?EmployeePhone $phone,
        private readonly ?EmployeeNid $nid,
        private readonly ?EmployeeCode $code,
        private EmployeeIsActive $isActive,
        private readonly ?EmployeeContract $contract,
        private readonly ?EmployeePlanification $planification,
        private readonly array $workplaceIds,
    ) {}

    public static function create(
        EmployeeId $id,
        EmployeeName $name,
        EmployeeLastName $lastName,
        EmployeeEmail $email,
        ?EmployeePhone $phone = null,
        ?EmployeeNid $nid = null,
        ?EmployeeCode $code = null,
        ?EmployeeContract $contract = null,
        ?EmployeePlanification $planification = null,
        array $workplaceIds = [],
    ): self {
        $employee = new self(
            id: $id,
            name: $name,
            lastName: $lastName,
            email: $email,
            phone: $phone,
            nid: $nid,
            code: $code,
            isActive: EmployeeIsActive::active(),
            contract: $contract,
            planification: $planification,
            workplaceIds: $workplaceIds,
        );

        // Registrar evento de dominio
        $employee->recordEvent(new EmployeeCreatedEvent(
            aggregateId: (string) $id->value(),
            name: $name->value(),
            lastName: $lastName->value(),
            email: $email->value(),
        ));

        return $employee;
    }

    public static function fromPrimitives(array $data): self
    {
        return new self(
            id: EmployeeId::create($data['id']),
            name: EmployeeName::create($data['name']),
            lastName: EmployeeLastName::create($data['last_name']),
            email: EmployeeEmail::create($data['email']),
            phone: isset($data['phone']) ? EmployeePhone::create($data['phone']) : null,
            nid: isset($data['nid']) ? EmployeeNid::create($data['nid']) : null,
            code: isset($data['code']) ? EmployeeCode::create($data['code']) : null,
            isActive: EmployeeIsActive::fromBool($data['is_active'] ?? true),
            contract: isset($data['contract']) ? EmployeeContract::fromPrimitives($data['contract']) : null,
            planification: isset($data['planification']) ? EmployeePlanification::fromPrimitives($data['planification']) : null,
            workplaceIds: array_map(
                fn (int $id) => WorkplaceId::create($id),
                $data['workplace_ids'] ?? [],
            ),
        );
    }

    public function id(): EmployeeId
    {
        return $this->id;
    }

    public function name(): EmployeeName
    {
        return $this->name;
    }

    public function lastName(): EmployeeLastName
    {
        return $this->lastName;
    }

    public function fullName(): string
    {
        return $this->name->value() . ' ' . $this->lastName->value();
    }

    public function email(): EmployeeEmail
    {
        return $this->email;
    }

    public function phone(): ?EmployeePhone
    {
        return $this->phone;
    }

    public function nid(): ?EmployeeNid
    {
        return $this->nid;
    }

    public function code(): ?EmployeeCode
    {
        return $this->code;
    }

    public function isActive(): bool
    {
        return $this->isActive->value();
    }

    public function contract(): ?EmployeeContract
    {
        return $this->contract;
    }

    public function planification(): ?EmployeePlanification
    {
        return $this->planification;
    }

    /**
     * @return WorkplaceId[]
     */
    public function workplaceIds(): array
    {
        return $this->workplaceIds;
    }

    public function activate(): void
    {
        $this->isActive = EmployeeIsActive::active();
    }

    public function deactivate(): void
    {
        $this->isActive = EmployeeIsActive::inactive();
    }

    public function toggleActive(): void
    {
        $this->isActive = $this->isActive->toggle();
    }

    public function hasContract(): bool
    {
        return $this->contract !== null;
    }

    public function hasPlanification(): bool
    {
        return $this->planification !== null;
    }

    public function weeklyHours(): ?float
    {
        return $this->planification?->totalWeekHours();
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'last_name' => $this->lastName->value(),
            'email' => $this->email->value(),
            'phone' => $this->phone?->value(),
            'nid' => $this->nid?->value(),
            'code' => $this->code?->value(),
            'is_active' => $this->isActive->value(),
            'contract' => $this->contract?->toPrimitives(),
            'planification' => $this->planification?->toPrimitives(),
            'workplace_ids' => array_map(
                fn (WorkplaceId $id) => $id->value(),
                $this->workplaceIds,
            ),
        ];
    }
}
