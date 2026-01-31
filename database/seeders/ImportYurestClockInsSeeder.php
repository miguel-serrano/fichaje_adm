<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportYurestClockInsSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = base_path('fichaje_yurest.csv');

        if (!file_exists($csvPath)) {
            $this->command->error('Archivo fichaje_yurest.csv no encontrado.');
            return;
        }

        // Crear empleado si no existe
        $employee = DB::table('employees')->where('code', 'YUR-42193')->first();
        if (!$employee) {
            $employeeId = DB::table('employees')->insertGetId([
                'name' => 'Empleado',
                'last_name' => 'Yurest',
                'email' => 'empleado42193@yurest.com',
                'code' => 'YUR-42193',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $employeeId = $employee->id;
        }

        // Crear centro de trabajo si no existe
        $workplace = DB::table('workplaces')->where('name', 'Local Yurest 22')->first();
        if (!$workplace) {
            $workplaceId = DB::table('workplaces')->insertGetId([
                'name' => 'Local Yurest 22',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $workplaceId = $workplace->id;
        }

        // Leer CSV
        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle); // saltar cabecera

        $entryCount = 0;
        $exitCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 36) {
                continue;
            }

            $fechaEntrada = trim($row[6], '"');
            $fechaSalida = trim($row[7], '"');
            $observaciones = trim($row[10], '"');
            $lat = !empty($row[17]) ? (float) $row[17] : 0.0;
            $long = !empty($row[18]) ? (float) $row[18] : 0.0;
            $activo = (int) $row[32];

            // Solo importar registros activos
            if (!$activo) {
                continue;
            }

            // Clock-in de ENTRADA
            if (!empty($fechaEntrada)) {
                DB::table('clock_ins')->insert([
                    'employee_id' => $employeeId,
                    'type' => 'entry',
                    'timestamp' => $fechaEntrada,
                    'workplace_id' => $workplaceId,
                    'latitude' => $lat,
                    'longitude' => $long,
                    'notes' => !empty($observaciones) ? $observaciones : null,
                    'created_at' => $fechaEntrada,
                    'updated_at' => $fechaEntrada,
                ]);
                $entryCount++;
            }

            // Clock-in de SALIDA
            if (!empty($fechaSalida)) {
                DB::table('clock_ins')->insert([
                    'employee_id' => $employeeId,
                    'type' => 'exit',
                    'timestamp' => $fechaSalida,
                    'workplace_id' => $workplaceId,
                    'latitude' => $lat,
                    'longitude' => $long,
                    'notes' => null,
                    'created_at' => $fechaSalida,
                    'updated_at' => $fechaSalida,
                ]);
                $exitCount++;
            }
        }

        fclose($handle);

        $this->command->info("Importados: {$entryCount} entradas, {$exitCount} salidas para empleado #{$employeeId} en centro #{$workplaceId}");
    }
}
