<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $employee_id
 * @property string $type
 * @property string $start_date
 * @property string|null $end_date
 * @property float $hours_per_week
 */
class EloquentEmployeeContractModel extends Model
{
    protected $table = 'employee_contracts';

    protected $fillable = [
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'hours_per_week',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'hours_per_week' => 'float',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EloquentEmployeeModel::class, 'employee_id');
    }
}
