<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Application\ListAll;

final class ListWorkplacesQuery
{
    public function __construct(
        public readonly int $activeUserId,
        public readonly bool $onlyActive = true,
    ) {}
}
