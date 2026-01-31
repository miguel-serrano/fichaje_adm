<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\User\Infrastructure\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property bool $is_super_admin
 * @property \Illuminate\Database\Eloquent\Collection<EloquentPermissionModel> $permissions
 */
class EloquentRoleModel extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_super_admin',
    ];

    protected $casts = [
        'is_super_admin' => 'boolean',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            EloquentPermissionModel::class,
            'role_permissions',
            'role_id',
            'permission_id',
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(EloquentUserModel::class, 'role_id');
    }
}
