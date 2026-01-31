<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->createPermissions();
        $this->createRoles();
        $this->assignPermissionsToRoles();
    }

    private function createPermissions(): void
    {
        $resources = [
            'clock_in' => ['view', 'create', 'edit', 'delete'],
            'employee' => ['view', 'create', 'edit', 'delete'],
            'workplace' => ['view', 'create', 'edit', 'delete'],
            'absence' => ['view', 'create', 'edit', 'delete'],
            'report' => ['view', 'export'],
        ];

        $permissions = [];
        $now = now();

        foreach ($resources as $resource => $actions) {
            foreach ($actions as $action) {
                $permissions[] = [
                    'name' => "{$resource}.{$action}",
                    'resource' => $resource,
                    'action' => $action,
                    'description' => ucfirst($action) . ' ' . str_replace('_', ' ', $resource),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('permissions')->insert($permissions);
    }

    private function createRoles(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrador',
                'description' => 'Acceso total al sistema',
                'is_super_admin' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrador',
                'description' => 'Gestión completa excepto configuración del sistema',
                'is_super_admin' => false,
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Gestión de empleados y fichajes de su equipo',
                'is_super_admin' => false,
            ],
            [
                'name' => 'employee',
                'display_name' => 'Empleado',
                'description' => 'Acceso básico para fichar',
                'is_super_admin' => false,
            ],
        ];

        $now = now();

        foreach ($roles as &$role) {
            $role['created_at'] = $now;
            $role['updated_at'] = $now;
        }

        DB::table('roles')->insert($roles);
    }

    private function assignPermissionsToRoles(): void
    {
        $rolePermissions = [
            'admin' => [
                'clock_in.*',
                'employee.*',
                'workplace.*',
                'absence.*',
                'report.*',
            ],
            'manager' => [
                'clock_in.view',
                'clock_in.create',
                'clock_in.edit',
                'employee.view',
                'absence.view',
                'absence.create',
                'absence.edit',
                'report.view',
            ],
            'employee' => [
                'clock_in.view',
                'clock_in.create',
            ],
        ];

        $allPermissions = DB::table('permissions')->get()->keyBy('name');
        $roles = DB::table('roles')->get()->keyBy('name');
        $now = now();

        foreach ($rolePermissions as $roleName => $permissionPatterns) {
            $role = $roles[$roleName] ?? null;
            if (!$role) {
                continue;
            }

            foreach ($permissionPatterns as $pattern) {
                if (str_ends_with($pattern, '.*')) {
                    // Wildcard: asignar todos los permisos del recurso
                    $resource = str_replace('.*', '', $pattern);
                    $matchingPermissions = $allPermissions->filter(
                        fn ($p) => $p->resource === $resource
                    );
                } else {
                    // Permiso específico
                    $matchingPermissions = collect([$allPermissions[$pattern] ?? null])->filter();
                }

                foreach ($matchingPermissions as $permission) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $role->id,
                        'permission_id' => $permission->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }
}
