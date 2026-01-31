<?php

declare(strict_types=1);

namespace App\DDD\Authorization\Domain\Interface;

enum VoteResult: string
{
    case GRANTED = 'granted';
    case DENIED = 'denied';
    case ABSTAIN = 'abstain';

    public function isGranted(): bool
    {
        return $this === self::GRANTED;
    }

    public function isDenied(): bool
    {
        return $this === self::DENIED;
    }

    public function isAbstain(): bool
    {
        return $this === self::ABSTAIN;
    }
}
