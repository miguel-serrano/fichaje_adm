<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\User\Infrastructure\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $resource
 * @property string $action
 * @property string|null $description
 */
class EloquentPermissionModel extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'resource',
        'action',
        'description',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            EloquentRoleModel::class,
            'role_permissions',
            'permission_id',
            'role_id',
        );
    }
}
