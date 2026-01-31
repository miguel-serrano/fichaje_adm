<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Domain\Voter;

use App\DDD\Authorization\Infrastructure\Service\Voter\AbstractPermissionVoter;

/**
 * Permisos disponibles:
 * - permission_employee_view
 * - permission_employee_create
 * - permission_employee_edit
 * - permission_employee_delete
 */
final class EmployeeVoter extends AbstractPermissionVoter
{
    private const VOTER_NAME = 'employee';

    protected function voterName(): string
    {
        return self::VOTER_NAME;
    }

    protected static function getStaticVoterName(): string
    {
        return self::VOTER_NAME;
    }
}
