<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\Entity;

use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeContractId;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeContractType;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeContractStartDate;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeContractEndDate;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeContractHoursPerWeek;

class EmployeeContract
{
    protected function __construct(
        private readonly EmployeeContractId $id,
        private readonly EmployeeContractType $type,
        private readonly EmployeeContractStartDate $startDate,
        private readonly ?EmployeeContractEndDate $endDate,
        private readonly EmployeeContractHoursPerWeek $hoursPerWeek,
    ) {}

    public static function create(
        EmployeeContractId $id,
        EmployeeContractType $type,
        EmployeeContractStartDate $startDate,
        EmployeeContractHoursPerWeek $hoursPerWeek,
        ?EmployeeContractEndDate $endDate = null,
    ): self {
        return new self(
            id: $id,
            type: $type,
            startDate: $startDate,
            endDate: $endDate,
            hoursPerWeek: $hoursPerWeek,
        );
    }

    public static function fromPrimitives(array $data): self
    {
        return new self(
            id: EmployeeContractId::create($data['id']),
            type: EmployeeContractType::from($data['type']),
            startDate: EmployeeContractStartDate::fromString($data['start_date']),
            endDate: isset($data['end_date']) ? EmployeeContractEndDate::fromString($data['end_date']) : null,
            hoursPerWeek: EmployeeContractHoursPerWeek::create($data['hours_per_week']),
        );
    }

    public function id(): EmployeeContractId
    {
        return $this->id;
    }

    public function type(): EmployeeContractType
    {
        return $this->type;
    }

    public function startDate(): EmployeeContractStartDate
    {
        return $this->startDate;
    }

    public function endDate(): ?EmployeeContractEndDate
    {
        return $this->endDate;
    }

    public function hoursPerWeek(): float
    {
        return $this->hoursPerWeek->value();
    }

    public function isIndefinite(): bool
    {
        return $this->endDate === null;
    }

    public function isActive(): bool
    {
        $today = new \DateTimeImmutable();

        if ($this->startDate->value() > $today) {
            return false;
        }

        if ($this->endDate !== null && $this->endDate->value() < $today) {
            return false;
        }

        return true;
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id->value(),
            'type' => $this->type->value,
            'start_date' => $this->startDate->toString(),
            'end_date' => $this->endDate?->toString(),
            'hours_per_week' => $this->hoursPerWeek->value(),
        ];
    }
}
