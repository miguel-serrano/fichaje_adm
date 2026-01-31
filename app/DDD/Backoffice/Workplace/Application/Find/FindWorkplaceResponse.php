<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Application\Find;

use App\DDD\Backoffice\Workplace\Domain\Workplace;

final class FindWorkplaceResponse
{
    private function __construct(
        private readonly array $data,
    ) {}

    public static function fromWorkplace(Workplace $workplace): self
    {
        return new self($workplace->toPrimitives());
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
