<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\User\Infrastructure\Mapper;

use App\DDD\Backoffice\User\Domain\Role;
use App\DDD\Backoffice\User\Domain\User;
use App\DDD\Backoffice\User\Infrastructure\Repository\EloquentRoleModel;
use App\DDD\Backoffice\User\Infrastructure\Repository\EloquentUserModel;

final class UserMapper
{
    public function toDomain(EloquentUserModel $model): User
    {
        $role = $model->role;
        $permissions = [];

        if ($role !== null) {
            $permissions = $role->permissions->pluck('name')->toArray();
        }

        return User::fromPrimitives([
            'id' => $model->id,
            'name' => $model->name,
            'email' => $model->email,
            'role' => $role !== null ? $this->mapRole($role) : null,
            'permissions' => $permissions,
            'is_super_admin' => $role?->is_super_admin ?? false,
        ]);
    }

    private function mapRole(EloquentRoleModel $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'display_name' => $role->display_name,
            'is_super_admin' => $role->is_super_admin,
            'permissions' => $role->permissions->pluck('name')->toArray(),
        ];
    }
}
