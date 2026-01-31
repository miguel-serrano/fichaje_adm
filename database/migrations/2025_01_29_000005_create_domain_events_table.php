<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_events', function (Blueprint $table) {
            $table->uuid('event_id')->primary();
            $table->string('event_name', 100)->index();
            $table->string('aggregate_id', 100)->index();
            $table->json('payload');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_on')->index();
            $table->timestamp('published_at')->nullable()->index(); // null = pendiente
            $table->timestamps();

            // Índice compuesto para outbox query
            $table->index(['published_at', 'occurred_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_events');
    }
};
