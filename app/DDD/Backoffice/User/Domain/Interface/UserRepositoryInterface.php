<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\User\Domain\Interface;

use App\DDD\Backoffice\User\Domain\User;
use App\DDD\Backoffice\User\Domain\ValueObject\UserId;

interface UserRepositoryInterface
{
    public function findById(UserId $id): ?User;

    public function findByEmail(string $email): ?User;

    public function save(User $user): void;
}
