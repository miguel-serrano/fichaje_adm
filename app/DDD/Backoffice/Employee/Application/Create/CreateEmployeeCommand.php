<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Application\Create;

final class CreateEmployeeCommand
{
    public function __construct(
        public readonly int $activeUserId,
        public readonly string $name,
        public readonly string $lastName,
        public readonly string $email,
        public readonly ?string $phone = null,
        public readonly ?string $nid = null,
        public readonly ?string $code = null,
    ) {}
}
