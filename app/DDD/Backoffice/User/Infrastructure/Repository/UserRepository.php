<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\User\Infrastructure\Repository;

use App\DDD\Backoffice\User\Domain\Interface\UserRepositoryInterface;
use App\DDD\Backoffice\User\Domain\User;
use App\DDD\Backoffice\User\Domain\ValueObject\UserId;
use App\DDD\Backoffice\User\Infrastructure\Mapper\UserMapper;

final class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly UserMapper $mapper,
    ) {}

    public function findById(UserId $id): ?User
    {
        $model = EloquentUserModel::with(['role.permissions'])
            ->find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function findByEmail(string $email): ?User
    {
        $model = EloquentUserModel::with(['role.permissions'])
            ->where('email', $email)
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function save(User $user): void
    {
        $data = $user->toPrimitives();

        EloquentUserModel::updateOrCreate(
            ['id' => $data['id']],
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'role_id' => $data['role']['id'] ?? null,
            ],
        );
    }
}
