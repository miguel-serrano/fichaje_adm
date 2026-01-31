<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Crear empleados
        $employees = [
            [
                'id' => 1,
                'name' => 'Juan',
                'last_name' => 'García López',
                'email' => 'juan.garcia@empresa.com',
                'phone' => '+34612345678',
                'nid' => '12345678Z',
                'code' => 'EMP001',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'María',
                'last_name' => 'Rodríguez Martín',
                'email' => 'maria.rodriguez@empresa.com',
                'phone' => '+34623456789',
                'nid' => '23456789A',
                'code' => 'EMP002',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Carlos',
                'last_name' => 'Fernández Ruiz',
                'email' => 'carlos.fernandez@empresa.com',
                'phone' => '+34634567890',
                'nid' => '34567890B',
                'code' => 'EMP003',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('employees')->insert($employees);

        // Crear centros de trabajo
        $workplaces = [
            [
                'id' => 1,
                'name' => 'Oficina Central',
                'address' => 'Calle Gran Vía 123',
                'city' => 'Madrid',
                'postal_code' => '28013',
                'latitude' => 40.420000,
                'longitude' => -3.705000,
                'radius' => 100,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Sucursal Norte',
                'address' => 'Paseo de la Castellana 50',
                'city' => 'Madrid',
                'postal_code' => '28046',
                'latitude' => 40.450000,
                'longitude' => -3.690000,
                'radius' => 150,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('workplaces')->insert($workplaces);

        // Crear fichajes de hoy
        $today = now()->format('Y-m-d');
        $clockIns = [
            [
                'employee_id' => 1,
                'type' => 'entry',
                'timestamp' => $today . ' 08:55:00',
                'workplace_id' => 1,
                'latitude' => 40.420100,
                'longitude' => -3.705100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 1,
                'type' => 'exit',
                'timestamp' => $today . ' 14:00:00',
                'workplace_id' => 1,
                'latitude' => 40.420100,
                'longitude' => -3.705100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 2,
                'type' => 'entry',
                'timestamp' => $today . ' 09:10:00',
                'workplace_id' => 1,
                'latitude' => 40.420200,
                'longitude' => -3.705200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 3,
                'type' => 'entry',
                'timestamp' => $today . ' 08:45:00',
                'workplace_id' => 2,
                'latitude' => 40.450100,
                'longitude' => -3.690100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('clock_ins')->insert($clockIns);
    }
}
