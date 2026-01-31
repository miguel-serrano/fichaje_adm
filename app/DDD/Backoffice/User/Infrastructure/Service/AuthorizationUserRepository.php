<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\User\Infrastructure\Service;

use App\DDD\Authorization\Domain\AuthorizableUser;
use App\DDD\Authorization\Domain\Interface\UserRepositoryInterface as AuthUserRepositoryInterface;
use App\DDD\Backoffice\User\Domain\Interface\UserRepositoryInterface;
use App\DDD\Backoffice\User\Domain\ValueObject\UserId;

/**
 * Adaptador que implementa la interfaz de Authorization
 * usando el repositorio de User del Backoffice
 */
final class AuthorizationUserRepository implements AuthUserRepositoryInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function findById(int $userId): ?AuthorizableUser
    {
        return $this->userRepository->findById(UserId::create($userId));
    }
}
