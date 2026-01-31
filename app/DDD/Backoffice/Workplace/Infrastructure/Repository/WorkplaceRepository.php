<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Infrastructure\Repository;

use App\DDD\Backoffice\Workplace\Domain\Interface\WorkplaceRepositoryInterface;
use App\DDD\Backoffice\Workplace\Domain\ValueObject\WorkplaceId;
use App\DDD\Backoffice\Workplace\Domain\Workplace;
use App\DDD\Backoffice\Workplace\Infrastructure\Mapper\WorkplaceMapper;

final class WorkplaceRepository implements WorkplaceRepositoryInterface
{
    public function __construct(
        private readonly WorkplaceMapper $mapper,
    ) {}

    public function findById(WorkplaceId $id): ?Workplace
    {
        $model = EloquentWorkplaceModel::find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function save(Workplace $workplace): void
    {
        $data = $workplace->toPrimitives();

        EloquentWorkplaceModel::updateOrCreate(
            ['id' => $data['id']],
            [
                'name' => $data['name'],
                'address' => $data['address'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'radius' => $data['radius'],
                'is_active' => $data['is_active'],
            ],
        );
    }

    public function delete(Workplace $workplace): void
    {
        EloquentWorkplaceModel::destroy($workplace->id()->value());
    }

    public function findAll(): array
    {
        return EloquentWorkplaceModel::all()
            ->map(fn ($model) => $this->mapper->toDomain($model))
            ->toArray();
    }

    public function findActive(): array
    {
        return EloquentWorkplaceModel::where('is_active', true)
            ->get()
            ->map(fn ($model) => $this->mapper->toDomain($model))
            ->toArray();
    }

    public function nextId(): WorkplaceId
    {
        $lastId = EloquentWorkplaceModel::max('id') ?? 0;
        return WorkplaceId::create($lastId + 1);
    }
}
