<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla principal de empleados
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('nid')->nullable()->unique(); // DNI/NIF
            $table->string('code')->nullable()->unique(); // Código interno
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        // Contratos de empleados
        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->enum('type', ['indefinite', 'temporary', 'part_time', 'internship', 'freelance']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('hours_per_week', 5, 2)->default(40);
            $table->timestamps();

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');

            $table->index('employee_id');
        });

        // Planificación semanal de empleados
        Schema::create('employee_planifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->json('week_schedule'); // Horario por día de la semana
            $table->decimal('total_week_hours', 5, 2);
            $table->timestamps();

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');

            $table->index('employee_id');
        });

        // Relación empleados-centros de trabajo (N:M)
        Schema::create('employee_workplace', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('workplace_id');
            $table->timestamps();

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');

            $table->unique(['employee_id', 'workplace_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_workplace');
        Schema::dropIfExists('employee_planifications');
        Schema::dropIfExists('employee_contracts');
        Schema::dropIfExists('employees');
    }
};
