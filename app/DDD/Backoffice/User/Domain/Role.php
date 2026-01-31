<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\User\Domain;

use App\DDD\Backoffice\User\Domain\ValueObject\RoleId;
use App\DDD\Backoffice\User\Domain\ValueObject\RoleName;

class Role
{
    /**
     * @param string[] $permissions
     */
    protected function __construct(
        private readonly RoleId $id,
        private readonly RoleName $name,
        private readonly string $displayName,
        private readonly bool $isSuperAdmin,
        private readonly array $permissions,
    ) {}

    public static function fromPrimitives(array $data): self
    {
        return new self(
            id: RoleId::create($data['id']),
            name: RoleName::create($data['name']),
            displayName: $data['display_name'],
            isSuperAdmin: $data['is_super_admin'] ?? false,
            permissions: $data['permissions'] ?? [],
        );
    }

    public function id(): RoleId
    {
        return $this->id;
    }

    public function name(): RoleName
    {
        return $this->name;
    }

    public function displayName(): string
    {
        return $this->displayName;
    }

    public function isSuperAdmin(): bool
    {
        return $this->isSuperAdmin;
    }

    /**
     * @return string[]
     */
    public function permissions(): array
    {
        return $this->permissions;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin) {
            return true;
        }

        return in_array($permission, $this->permissions, true);
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'display_name' => $this->displayName,
            'is_super_admin' => $this->isSuperAdmin,
            'permissions' => $this->permissions,
        ];
    }
}
