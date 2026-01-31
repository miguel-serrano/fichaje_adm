<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Domain\Voter;

use App\DDD\Authorization\Infrastructure\Service\Voter\AbstractPermissionVoter;

/**
 * Permisos disponibles:
 * - permission_workplace_view
 * - permission_workplace_create
 * - permission_workplace_edit
 * - permission_workplace_delete
 */
final class WorkplaceVoter extends AbstractPermissionVoter
{
    private const VOTER_NAME = 'workplace';

    protected function voterName(): string
    {
        return self::VOTER_NAME;
    }

    protected static function getStaticVoterName(): string
    {
        return self::VOTER_NAME;
    }
}
