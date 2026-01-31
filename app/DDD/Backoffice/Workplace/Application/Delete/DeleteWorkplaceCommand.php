<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Application\Delete;

final class DeleteWorkplaceCommand
{
    public function __construct(
        public readonly int $activeUserId,
        public readonly int $workplaceId,
    ) {}
}
