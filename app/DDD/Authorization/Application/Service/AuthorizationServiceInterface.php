<?php

declare(strict_types=1);

namespace App\DDD\Authorization\Application\Service;

interface AuthorizationServiceInterface
{
    public function isGranted(string $attribute, int $userId, mixed $subject = null): bool;

    /**
     * @throws \App\DDD\Authorization\Domain\Exception\AccessDeniedException
     */
    public function denyAccessUnlessGranted(string $attribute, int $userId, mixed $subject = null): void;
}
