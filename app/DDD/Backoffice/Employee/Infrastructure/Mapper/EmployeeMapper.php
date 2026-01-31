<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Mapper;

use App\DDD\Backoffice\Employee\Domain\Employee;
use App\DDD\Backoffice\Employee\Infrastructure\Repository\EloquentEmployeeModel;

final class EmployeeMapper
{
    public function toDomain(EloquentEmployeeModel $model): Employee
    {
        $contract = $model->contract;
        $planification = $model->planification;

        return Employee::fromPrimitives([
            'id' => $model->id,
            'name' => $model->name,
            'last_name' => $model->last_name,
            'email' => $model->email,
            'phone' => $model->phone,
            'nid' => $model->nid,
            'code' => $model->code,
            'is_active' => $model->is_active,
            'contract' => $contract !== null ? [
                'id' => $contract->id,
                'type' => $contract->type,
                'start_date' => $contract->start_date->format('Y-m-d'),
                'end_date' => $contract->end_date?->format('Y-m-d'),
                'hours_per_week' => $contract->hours_per_week,
            ] : null,
            'planification' => $planification !== null ? [
                'id' => $planification->id,
                'week_schedule' => $planification->week_schedule,
                'total_week_hours' => $planification->total_week_hours,
            ] : null,
            'workplace_ids' => $model->workplaces->pluck('id')->toArray(),
        ]);
    }
}
