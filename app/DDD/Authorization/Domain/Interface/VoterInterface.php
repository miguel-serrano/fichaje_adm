<?php

declare(strict_types=1);

namespace App\DDD\Authorization\Domain\Interface;

interface VoterInterface
{
    /**
     * @return string[]
     */
    public function supportedAttributes(): array;

    public function vote(int $userId, string $attribute, mixed $subject = null): VoteResult;
}
