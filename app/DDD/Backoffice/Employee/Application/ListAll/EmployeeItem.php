<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\ListAll;

use App\DDD\Backoffice\Employee\Domain\Employee;

final class EmployeeItem
{
    private function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $lastName,
        public readonly string $fullName,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $code,
        public readonly bool $isActive,
        public readonly ?float $weeklyHours,
    ) {}

    public static function fromEmployee(Employee $employee): self
    {
        return new self(
            id: $employee->id()->value(),
            name: $employee->name()->value(),
            lastName: $employee->lastName()->value(),
            fullName: $employee->fullName(),
            email: $employee->email()->value(),
            phone: $employee->phone()?->value(),
            code: $employee->code()?->value(),
            isActive: $employee->isActive(),
            weeklyHours: $employee->weeklyHours(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->lastName,
            'full_name' => $this->fullName,
            'email' => $this->email,
            'phone' => $this->phone,
            'code' => $this->code,
            'is_active' => $this->isActive,
            'weekly_hours' => $this->weeklyHours,
        ];
    }
}
