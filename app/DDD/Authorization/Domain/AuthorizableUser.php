<?php

declare(strict_types=1);

namespace App\DDD\Authorization\Domain;

interface AuthorizableUser
{
    public function id(): int;

    public function isSuperAdmin(): bool;

    public function hasPermission(string $permission): bool;

    /**
     * @return string[]
     */
    public function permissions(): array;
}
