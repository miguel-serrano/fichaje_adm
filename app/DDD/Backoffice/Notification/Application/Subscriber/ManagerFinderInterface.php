<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Notification\Application\Subscriber;

/**
 * Interface para encontrar el manager de un empleado
 */
interface ManagerFinderInterface
{
    /**
     * Encuentra el ID del manager/supervisor de un empleado
     * Retorna null si no tiene manager asignado
     */
    public function findManagerFor(int $employeeId): ?int;

    /**
     * Encuentra los IDs de todos los admins del sistema
     *
     * @return int[]
     */
    public function findAllAdmins(): array;
}
