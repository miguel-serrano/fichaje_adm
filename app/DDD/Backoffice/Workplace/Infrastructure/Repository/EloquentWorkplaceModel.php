<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Workplace\Infrastructure\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property string|null $city
 * @property string|null $postal_code
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int|null $radius
 * @property bool $is_active
 */
class EloquentWorkplaceModel extends Model
{
    protected $table = 'workplaces';

    protected $fillable = [
        'name',
        'address',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'radius',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius' => 'integer',
        'is_active' => 'boolean',
    ];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\DDD\Backoffice\Employee\Infrastructure\Repository\EloquentEmployeeModel::class,
            'employee_workplace',
            'workplace_id',
            'employee_id',
        );
    }
}
