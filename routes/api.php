<?php

use App\Http\Controllers\API\DashboardApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    // Dashboard API Routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardApiController::class, 'stats']);
        Route::get('/recent-activities', [DashboardApiController::class, 'recentActivities']);
        Route::get('/projects-progress', [DashboardApiController::class, 'projectsProgress']);
    });
});