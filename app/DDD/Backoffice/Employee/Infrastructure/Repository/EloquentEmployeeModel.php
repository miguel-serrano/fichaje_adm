<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string|null $nid
 * @property string|null $code
 * @property bool $is_active
 */
class EloquentEmployeeModel extends Model
{
    protected $table = 'employees';

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'nid',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function contract(): HasOne
    {
        return $this->hasOne(EloquentEmployeeContractModel::class, 'employee_id');
    }

    public function planification(): HasOne
    {
        return $this->hasOne(EloquentEmployeePlanificationModel::class, 'employee_id');
    }

    public function workplaces(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\DDD\Backoffice\Workplace\Infrastructure\Repository\EloquentWorkplaceModel::class,
            'employee_workplace',
            'employee_id',
            'workplace_id',
        );
    }
}
