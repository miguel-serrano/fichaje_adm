<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Domain\Voter;

use App\DDD\Authorization\Infrastructure\Service\Voter\AbstractPermissionVoter;

/**
 * Permisos disponibles:
 * - permission_clock_in_view
 * - permission_clock_in_create
 * - permission_clock_in_edit
 * - permission_clock_in_delete
 */
final class ClockInVoter extends AbstractPermissionVoter
{
    private const VOTER_NAME = 'clock_in';

    protected function voterName(): string
    {
        return self::VOTER_NAME;
    }

    protected static function getStaticVoterName(): string
    {
        return self::VOTER_NAME;
    }
}
