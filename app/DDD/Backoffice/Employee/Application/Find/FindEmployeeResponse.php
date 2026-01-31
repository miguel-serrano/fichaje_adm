<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Find;

use App\DDD\Backoffice\Employee\Domain\Employee;

final class FindEmployeeResponse
{
    private function __construct(
        private readonly array $data,
    ) {}

    public static function fromEmployee(Employee $employee): self
    {
        return new self($employee->toPrimitives());
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
