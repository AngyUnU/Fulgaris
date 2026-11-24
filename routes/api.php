<?php

use App\Http\Controllers\Api\MobileSyncController;
use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes para BeTrack (Integración con App Móvil Ionic / Angular)
|--------------------------------------------------------------------------
|
| Rutas protegidas mediante Laravel Sanctum con respuestas estandarizadas en JSON.
|
*/

Route::prefix('v1')->group(function () {
    // Autenticación desde la aplicación móvil
    Route::post('/login', [MobileSyncController::class, 'login']);

    // Rutas protegidas para integrantes móviles
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [MobileSyncController::class, 'me']);
        Route::get('/my-activities', [MobileSyncController::class, 'myActivities']);
        Route::post('/activities/{activity}/status', [MobileSyncController::class, 'updateStatus']);
        Route::get('/activity-comments/{comment}/evidence', [ActivityController::class, 'evidence']);
        Route::post('/logout', [MobileSyncController::class, 'logout']);
    });
});
