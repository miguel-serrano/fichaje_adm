<?php

declare(strict_types=1);

namespace App\DDD\Authorization\Domain\Interface;

use App\DDD\Authorization\Domain\AuthorizableUser;

interface UserRepositoryInterface
{
    public function findById(int $userId): ?AuthorizableUser;
}
