<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Cargar rutas de eventos
require __DIR__.'/events.php';
