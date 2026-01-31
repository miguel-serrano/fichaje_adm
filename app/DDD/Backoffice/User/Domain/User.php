<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\User\Domain;

use App\DDD\Authorization\Domain\AuthorizableUser;
use App\DDD\Backoffice\User\Domain\ValueObject\UserId;
use App\DDD\Backoffice\User\Domain\ValueObject\UserEmail;
use App\DDD\Backoffice\User\Domain\ValueObject\UserName;

class User implements AuthorizableUser
{
    /**
     * @param string[] $permissions
     */
    protected function __construct(
        private readonly UserId $userId,
        private readonly UserName $name,
        private readonly UserEmail $email,
        private readonly ?Role $role,
        private readonly array $permissions,
        private readonly bool $isSuperAdmin,
    ) {}

    public static function fromPrimitives(array $data): self
    {
        return new self(
            userId: UserId::create($data['id']),
            name: UserName::create($data['name']),
            email: UserEmail::create($data['email']),
            role: isset($data['role']) ? Role::fromPrimitives($data['role']) : null,
            permissions: $data['permissions'] ?? [],
            isSuperAdmin: $data['is_super_admin'] ?? false,
        );
    }

    public function id(): int
    {
        return $this->userId->value();
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function name(): UserName
    {
        return $this->name;
    }

    public function email(): UserEmail
    {
        return $this->email;
    }

    public function role(): ?Role
    {
        return $this->role;
    }

    public function isSuperAdmin(): bool
    {
        return $this->isSuperAdmin || ($this->role?->isSuperAdmin() ?? false);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissions, true);
    }

    /**
     * @return string[]
     */
    public function permissions(): array
    {
        return $this->permissions;
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->userId->value(),
            'name' => $this->name->value(),
            'email' => $this->email->value(),
            'role' => $this->role?->toPrimitives(),
            'permissions' => $this->permissions,
            'is_super_admin' => $this->isSuperAdmin(),
        ];
    }
}
