<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Workplace;

final class UpdateEmployeeWorkplacesCommand
{
    /**
     * @param int[] $workplaceIds
     */
    public function __construct(
        public readonly int $activeUserId,
        public readonly int $employeeId,
        public readonly array $workplaceIds,
    ) {}
}
