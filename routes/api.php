<?php

use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\DashboardController;
use \App\Http\Controllers\v1\ProjectController;
use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\EnumViewController;
use App\Http\Controllers\v1\ExternalApiViewController;
use \App\Http\Controllers\v1\LogController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

RateLimiter::for('map', function (Request $request) {
    return Limit::perMinute(500)->by($request->ip());
});

// API v1 routes
Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    // Protected routes - using the built-in JWT auth middleware
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);

        // Projects
        Route::post('projects/search', [ProjectController::class, 'search'])->name('projects.search'); // Search projects with filters
        Route::post('projects/map', [ProjectController::class, 'map'])->name('projects.map')->middleware('throttle:map');; // Get projects for map with filters
        Route::post('projects', [ProjectController::class, 'store'])->name('projects.store'); // Create a new project
        Route::get('projects/{id}', [ProjectController::class, 'show'])->where('id', '[0-9]+'); // Get a project details
        Route::get('projects/{id}/editors-history', [ProjectController::class, 'editorsHistory']); // Get all versions of a project
        Route::get('projects/{id}/log', [ProjectController::class, 'projectLog']); // Get all versions of a project
        
        // Dashboard
        Route::get('dashboard/statistics', [DashboardController::class, 'getStatistics']);
        Route::get('dashboard/calendar', [DashboardController::class, 'getCalendar']);

        // Users
        Route::apiResource('users', UserController::class); // Full CRUD operations for users

        // Logs
        Route::get('logs', [LogController::class, 'index'])
            ->name('logs.index'); // Get all system action logs

        // Enums and Views routes - with type constraint
        Route::get('{type}/{model}/{id?}', [EnumViewController::class, 'index'])
            ->where('type', 'enums|views');
        Route::get('{type}/{model}/{id}/{relation}', [EnumViewController::class, 'relation'])
            ->where('type', 'enums|views');
        Route::post('{type}/{model}', [EnumViewController::class, 'store'])
            ->where('type', 'enums|views');
        Route::put('{type}/{model}/{id}', [EnumViewController::class, 'update'])
            ->where('type', 'enums|views');
        Route::delete('{type}/{model}/{id}', [EnumViewController::class, 'destroy'])
            ->where('type', 'enums|views');

        Route::get('external', [ExternalApiViewController::class, 'index']);

        });
});
