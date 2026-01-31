<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\Interface;

use App\DDD\Backoffice\Employee\Domain\Employee;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;

interface EmployeeRepositoryInterface
{
    public function findById(EmployeeId $id): ?Employee;

    public function save(Employee $employee): void;

    public function delete(Employee $employee): void;

    /**
     * @return Employee[]
     */
    public function findAll(): array;

    /**
     * @return Employee[]
     */
    public function findActive(): array;

    public function nextId(): EmployeeId;
}
