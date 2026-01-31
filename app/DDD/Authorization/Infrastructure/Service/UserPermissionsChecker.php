<?php

declare(strict_types=1);

namespace App\DDD\Authorization\Infrastructure\Service;

use App\DDD\Authorization\Domain\Interface\UserRepositoryInterface;

final class UserPermissionsChecker implements UserPermissionsCheckerInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function hasPermission(int $userId, string $resource, string $action): bool
    {
        if ($this->isSuperAdmin($userId)) {
            return true;
        }

        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            return false;
        }

        $permission = $resource . '.' . $action;

        return $user->hasPermission($permission);
    }

    public function isSuperAdmin(int $userId): bool
    {
        $user = $this->userRepository->findById($userId);

        return $user?->isSuperAdmin() ?? false;
    }
}
