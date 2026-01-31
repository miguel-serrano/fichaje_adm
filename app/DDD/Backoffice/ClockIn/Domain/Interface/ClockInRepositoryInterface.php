<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\Interface;

use App\DDD\Backoffice\ClockIn\Domain\ClockIn;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInId;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use DateTimeImmutable;

interface ClockInRepositoryInterface
{
    public function save(ClockIn $clockIn): void;

    public function search(ClockInId $id): ?ClockIn;

    public function findById(ClockInId $id): ClockIn;

    public function delete(ClockIn $clockIn): void;

    /**
     * @return ClockIn[]
     */
    public function findByEmployeeAndDateRange(
        EmployeeId $employeeId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
    ): array;

    public function findLastByEmployee(EmployeeId $employeeId): ?ClockIn;

    public function nextId(): ClockInId;
}
