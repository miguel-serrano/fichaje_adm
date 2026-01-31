<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\ListAll;

final class ListEmployeesQuery
{
    public function __construct(
        public readonly int $activeUserId,
        public readonly bool $onlyActive = true,
    ) {}
}
