<?php

declare(strict_types=1);

namespace App\DDD\Authorization\Infrastructure\Service;

interface UserPermissionsCheckerInterface
{
    public function hasPermission(int $userId, string $resource, string $action): bool;

    public function isSuperAdmin(int $userId): bool;
}
