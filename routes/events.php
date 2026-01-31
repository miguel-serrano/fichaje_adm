<?php

declare(strict_types=1);

use App\DDD\Shared\Infrastructure\Http\Controller\EventStatsController;
use App\DDD\Shared\Infrastructure\Http\Controller\ListEventsController;
use App\DDD\Shared\Infrastructure\Http\Controller\SearchEventsController;
use App\DDD\Shared\Infrastructure\Http\Controller\SyncEventsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Events API Routes
|--------------------------------------------------------------------------
|
| Rutas para gestionar y sincronizar eventos de dominio
|
*/

Route::prefix('events')->middleware(['auth:sanctum'])->group(function () {
    // Listar eventos desde MySQL
    Route::get('/', ListEventsController::class)->name('events.list');

    // Buscar eventos en Elasticsearch (si está habilitado)
    Route::get('/search', SearchEventsController::class)->name('events.search');

    // Estadísticas
    Route::get('/stats', EventStatsController::class)->name('events.stats');

    // Sincronizar pendientes a Elasticsearch
    Route::post('/sync', [SyncEventsController::class, 'sync'])->name('events.sync');

    // Re-encolar eventos para re-sincronizar
    Route::post('/resync', [SyncEventsController::class, 'resync'])->name('events.resync');
});
