<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Infrastructure\Service;

use App\DDD\Backoffice\Notification\Application\Subscriber\ManagerFinderInterface;
use App\DDD\Backoffice\User\Infrastructure\Repository\EloquentUserModel;

/**
 * Implementación simple que retorna el primer admin/manager
 * En producción, esto debería buscar el manager real del empleado
 */
final class SimpleManagerFinder implements ManagerFinderInterface
{
    public function findManagerFor(int $employeeId): ?int
    {
        // Por ahora, retorna el primer usuario con rol admin o manager
        $manager = EloquentUserModel::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin', 'manager', 'super_admin']);
        })->first();

        return $manager?->id;
    }

    public function findAllAdmins(): array
    {
        return EloquentUserModel::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin', 'super_admin']);
        })->pluck('id')->toArray();
    }
}
