<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workplaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedInteger('radius')->nullable()->comment('Radio de geofence en metros');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        // Añadir FK a employee_workplace si la tabla ya existe
        if (Schema::hasTable('employee_workplace')) {
            Schema::table('employee_workplace', function (Blueprint $table) {
                $table->foreign('workplace_id')
                    ->references('id')
                    ->on('workplaces')
                    ->onDelete('cascade');
            });
        }

        // Añadir FK a clock_ins si la tabla ya existe
        if (Schema::hasTable('clock_ins')) {
            Schema::table('clock_ins', function (Blueprint $table) {
                $table->foreign('workplace_id')
                    ->references('id')
                    ->on('workplaces')
                    ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('clock_ins')) {
            Schema::table('clock_ins', function (Blueprint $table) {
                $table->dropForeign(['workplace_id']);
            });
        }

        if (Schema::hasTable('employee_workplace')) {
            Schema::table('employee_workplace', function (Blueprint $table) {
                $table->dropForeign(['workplace_id']);
            });
        }

        Schema::dropIfExists('workplaces');
    }
};
