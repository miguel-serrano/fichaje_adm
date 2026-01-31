<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear permisos para cada recurso
        $resources = ['employee', 'workplace', 'clock_in', 'notification'];
        $actions = ['view', 'create', 'edit', 'delete'];
        $permissionIds = [];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permissionIds[] = DB::table('permissions')->insertGetId([
                    'name' => "{$resource}.{$action}",
                    'resource' => $resource,
                    'action' => $action,
                    'description' => ucfirst($action) . ' ' . str_replace('_', ' ', $resource),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Crear roles
        $superAdminRoleId = DB::table('roles')->insertGetId([
            'name' => 'super_admin',
            'display_name' => 'Super Administrador',
            'description' => 'Acceso total al sistema',
            'is_super_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'admin',
            'display_name' => 'Administrador',
            'description' => 'Gestión de empleados, centros y fichajes',
            'is_super_admin' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $managerRoleId = DB::table('roles')->insertGetId([
            'name' => 'manager',
            'display_name' => 'Responsable',
            'description' => 'Visualización y gestión de fichajes',
            'is_super_admin' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Asignar todos los permisos al role admin
        foreach ($permissionIds as $permissionId) {
            DB::table('role_permissions')->insert([
                'role_id' => $adminRoleId,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Asignar permisos de solo lectura + fichajes al role manager
        $managerPermissions = DB::table('permissions')
            ->where(function ($q) {
                $q->where('action', 'view')
                  ->orWhere('resource', 'clock_in');
            })
            ->pluck('id');

        foreach ($managerPermissions as $permissionId) {
            DB::table('role_permissions')->insert([
                'role_id' => $managerRoleId,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Crear usuario de prueba con role super_admin
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => $superAdminRoleId,
        ]);
    }
}
