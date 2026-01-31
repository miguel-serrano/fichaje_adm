<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Permisos disponibles en el sistema
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();         // ej: "clock_in.view"
            $table->string('resource');               // ej: "clock_in"
            $table->string('action');                 // ej: "view"
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('resource');
        });

        // Roles del sistema
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();         // ej: "admin", "manager", "employee"
            $table->string('display_name');           // ej: "Administrador"
            $table->string('description')->nullable();
            $table->boolean('is_super_admin')->default(false);
            $table->timestamps();
        });

        // Permisos asignados a cada rol (N:M)
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->unique(['role_id', 'permission_id']);
        });

        // Rol asignado a cada usuario
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('id');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};
