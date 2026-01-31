<?php

declare(strict_types=1);

namespace App\DDD\Authorization\Infrastructure\Service\Voter;

use App\DDD\Authorization\Domain\Interface\VoterInterface;
use App\DDD\Authorization\Domain\Interface\VoteResult;

abstract class AbstractVoter implements VoterInterface
{
    public function vote(int $userId, string $attribute, mixed $subject = null): VoteResult
    {
        if (!$this->supports($attribute)) {
            return VoteResult::ABSTAIN;
        }

        return $this->voteOnAttribute($userId, $attribute, $subject);
    }

    protected function supports(string $attribute): bool
    {
        return in_array($attribute, $this->supportedAttributes(), true);
    }

    abstract protected function voteOnAttribute(int $userId, string $attribute, mixed $subject): VoteResult;

    protected function grantAccess(): VoteResult
    {
        return VoteResult::GRANTED;
    }

    protected function denyAccess(): VoteResult
    {
        return VoteResult::DENIED;
    }
}
