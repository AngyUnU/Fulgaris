<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AgreementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // 1. Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Proyectos
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store')->middleware('role:admin,lider');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update')->middleware('role:admin,lider');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy')->middleware('role:admin,lider');

    // 3. Actividades
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/activities/comments/{comment}/evidence', [ActivityController::class, 'evidence'])->name('activities.comments.evidence');
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store')->middleware('role:admin,lider,desarrollador,qa');
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update')->middleware('role:admin,lider,desarrollador,qa');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy')->middleware('role:admin,lider,desarrollador,qa');

    // 4. Acuerdos
    Route::get('/agreements', [AgreementController::class, 'index'])->name('agreements.index');
    Route::post('/agreements', [AgreementController::class, 'store'])->name('agreements.store')->middleware('role:admin,lider');
    Route::put('/agreements/{agreement}', [AgreementController::class, 'update'])->name('agreements.update')->middleware('role:admin,lider');
    Route::delete('/agreements/{agreement}', [AgreementController::class, 'destroy'])->name('agreements.destroy')->middleware('role:admin,lider');

    // 5. Supervisión y Seguimiento
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::post('/monitoring/comments', [MonitoringController::class, 'storeComment'])->name('monitoring.comments.store');

    // 6. Administración de Usuarios (solo administradores)
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('role:admin');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('role:admin');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role.update')->middleware('role:admin');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('role:admin');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
