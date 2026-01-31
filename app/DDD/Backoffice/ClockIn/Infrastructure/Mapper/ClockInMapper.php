<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Infrastructure\Mapper;

use App\DDD\Backoffice\ClockIn\Domain\ClockIn;
use App\DDD\Backoffice\ClockIn\Infrastructure\Repository\EloquentClockInModel;

final class ClockInMapper
{
    public function toDomain(EloquentClockInModel $model): ClockIn
    {
        return ClockIn::fromPrimitives([
            'id' => $model->id,
            'employee_id' => $model->employee_id,
            'type' => $model->type,
            'timestamp' => $model->timestamp->toIso8601String(),
            'workplace_id' => $model->workplace_id,
            'latitude' => $model->latitude,
            'longitude' => $model->longitude,
            'notes' => $model->notes,
            'created_at' => $model->created_at->toIso8601String(),
            'updated_at' => $model->updated_at?->toIso8601String(),
        ]);
    }
}
