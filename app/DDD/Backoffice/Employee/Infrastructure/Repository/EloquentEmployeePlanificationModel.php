<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $employee_id
 * @property array $week_schedule
 * @property float $total_week_hours
 */
class EloquentEmployeePlanificationModel extends Model
{
    protected $table = 'employee_planifications';

    protected $fillable = [
        'employee_id',
        'week_schedule',
        'total_week_hours',
    ];

    protected $casts = [
        'week_schedule' => 'array',
        'total_week_hours' => 'float',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EloquentEmployeeModel::class, 'employee_id');
    }
}
