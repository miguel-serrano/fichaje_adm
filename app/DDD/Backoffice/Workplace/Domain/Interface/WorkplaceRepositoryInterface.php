<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Domain\Interface;

use App\DDD\Backoffice\Workplace\Domain\Workplace;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceId;

interface WorkplaceRepositoryInterface
{
    public function findById(WorkplaceId $id): ?Workplace;

    public function save(Workplace $workplace): void;

    public function delete(Workplace $workplace): void;

    /**
     * @return Workplace[]
     */
    public function findAll(): array;

    /**
     * @return Workplace[]
     */
    public function findActive(): array;

    public function nextId(): WorkplaceId;
}
