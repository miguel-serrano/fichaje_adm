<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Infrastructure\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $employee_id
 * @property string $type
 * @property string $timestamp
 * @property int|null $workplace_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $notes
 * @property string $created_at
 * @property string|null $updated_at
 */
final class EloquentClockInModel extends Model
{
    protected $table = 'clock_ins';

    protected $fillable = [
        'employee_id',
        'type',
        'timestamp',
        'workplace_id',
        'latitude',
        'longitude',
        'notes',
    ];

    protected $casts = [
        'employee_id' => 'integer',
        'workplace_id' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'timestamp' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(
            \App\DDD\Backoffice\Employee\Infrastructure\Repository\EloquentEmployeeModel::class,
            'employee_id',
        );
    }
}
