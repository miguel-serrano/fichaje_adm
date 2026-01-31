<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Planification\Delete;

final class DeleteEmployeePlanificationCommand
{
    public function __construct(
        public readonly int $activeUserId,
        public readonly int $employeeId,
    ) {}
}
