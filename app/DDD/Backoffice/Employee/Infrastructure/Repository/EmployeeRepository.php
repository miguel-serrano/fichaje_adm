<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Repository;

use App\DDD\Backoffice\Employee\Domain\Employee;
use App\DDD\Backoffice\Employee\Domain\Interface\EmployeeRepositoryInterface;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use App\DDD\Backoffice\Employee\Infrastructure\Mapper\EmployeeMapper;

final class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function __construct(
        private readonly EmployeeMapper $mapper,
    ) {}

    public function findById(EmployeeId $id): ?Employee
    {
        $model = EloquentEmployeeModel::with(['contract', 'planification', 'workplaces'])
            ->find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function save(Employee $employee): void
    {
        $data = $employee->toPrimitives();

        $model = EloquentEmployeeModel::updateOrCreate(
            ['id' => $data['id']],
            [
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'nid' => $data['nid'],
                'code' => $data['code'],
                'is_active' => $data['is_active'],
            ],
        );

        // Guardar contrato si existe
        if ($data['contract'] !== null) {
            EloquentEmployeeContractModel::updateOrCreate(
                ['employee_id' => $model->id],
                [
                    'type' => $data['contract']['type'],
                    'start_date' => $data['contract']['start_date'],
                    'end_date' => $data['contract']['end_date'],
                    'hours_per_week' => $data['contract']['hours_per_week'],
                ],
            );
        }

        // Guardar planificación si existe
        if ($data['planification'] !== null) {
            EloquentEmployeePlanificationModel::updateOrCreate(
                ['employee_id' => $model->id],
                [
                    'week_schedule' => $data['planification']['week_schedule'],
                    'total_week_hours' => $data['planification']['total_week_hours'],
                ],
            );
        }

        // Sincronizar workplaces
        $model->workplaces()->sync($data['workplace_ids']);
    }

    public function delete(Employee $employee): void
    {
        EloquentEmployeeModel::destroy($employee->id()->value());
    }

    public function findAll(): array
    {
        return EloquentEmployeeModel::with(['contract', 'planification', 'workplaces'])
            ->get()
            ->map(fn ($model) => $this->mapper->toDomain($model))
            ->toArray();
    }

    public function findActive(): array
    {
        return EloquentEmployeeModel::with(['contract', 'planification', 'workplaces'])
            ->where('is_active', true)
            ->get()
            ->map(fn ($model) => $this->mapper->toDomain($model))
            ->toArray();
    }

    public function nextId(): EmployeeId
    {
        $lastId = EloquentEmployeeModel::max('id') ?? 0;
        return EmployeeId::create($lastId + 1);
    }
}
