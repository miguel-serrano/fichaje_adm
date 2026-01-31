<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Infrastructure\Repository;

use App\DDD\Backoffice\ClockIn\Domain\ClockIn;
use App\DDD\Backoffice\ClockIn\Domain\Exception\ClockInNotFoundException;
use App\DDD\Backoffice\ClockIn\Domain\Interface\ClockInRepositoryInterface;
use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInId;
use App\DDD\Backoffice\ClockIn\Infrastructure\Mapper\ClockInMapper;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeId;
use DateTimeImmutable;

final class ClockInRepository implements ClockInRepositoryInterface
{
    public function __construct(
        private readonly ClockInMapper $mapper,
    ) {}

    public function save(ClockIn $clockIn): void
    {
        $data = $clockIn->toPrimitives();

        EloquentClockInModel::updateOrCreate(
            ['id' => $data['id']],
            [
                'employee_id' => $data['employee_id'],
                'type' => $data['type'],
                'timestamp' => $data['timestamp'],
                'workplace_id' => $data['workplace_id'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'notes' => $data['notes'],
            ],
        );
    }

    public function search(ClockInId $id): ?ClockIn
    {
        $model = EloquentClockInModel::find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function findById(ClockInId $id): ClockIn
    {
        $clockIn = $this->search($id);

        if ($clockIn === null) {
            throw ClockInNotFoundException::withId($id);
        }

        return $clockIn;
    }

    public function delete(ClockIn $clockIn): void
    {
        EloquentClockInModel::destroy($clockIn->id()->value());
    }

    public function findByEmployeeAndDateRange(
        EmployeeId $employeeId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
    ): array {
        $models = EloquentClockInModel::query()
            ->where('employee_id', $employeeId->value())
            ->whereBetween('timestamp', [
                $startDate->format('Y-m-d 00:00:00'),
                $endDate->format('Y-m-d 23:59:59'),
            ])
            ->orderBy('timestamp', 'asc')
            ->get();

        return $models
            ->map(fn (EloquentClockInModel $model) => $this->mapper->toDomain($model))
            ->toArray();
    }

    public function findLastByEmployee(EmployeeId $employeeId): ?ClockIn
    {
        $model = EloquentClockInModel::query()
            ->where('employee_id', $employeeId->value())
            ->orderBy('timestamp', 'desc')
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function nextId(): ClockInId
    {
        $lastId = EloquentClockInModel::max('id') ?? 0;

        return ClockInId::create($lastId + 1);
    }
}
