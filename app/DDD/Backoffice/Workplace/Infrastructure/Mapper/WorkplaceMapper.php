<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Infrastructure\Mapper;

use App\DDD\Backoffice\Workplace\Domain\Workplace;
use App\DDD\Backoffice\Workplace\Infrastructure\Repository\EloquentWorkplaceModel;

final class WorkplaceMapper
{
    public function toDomain(EloquentWorkplaceModel $model): Workplace
    {
        return Workplace::fromPrimitives([
            'id' => $model->id,
            'name' => $model->name,
            'address' => $model->address,
            'city' => $model->city,
            'postal_code' => $model->postal_code,
            'latitude' => $model->latitude,
            'longitude' => $model->longitude,
            'radius' => $model->radius,
            'is_active' => $model->is_active,
        ]);
    }
}
