<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Contract\Delete;

final class DeleteEmployeeContractCommand
{
    public function __construct(
        public readonly int $activeUserId,
        public readonly int $employeeId,
    ) {}
}
