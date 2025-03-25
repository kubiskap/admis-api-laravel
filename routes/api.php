<?php

use App\Http\Controllers\v1\AuthController;
use \App\Http\Controllers\v1\ProjectController;
use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\EnumViewController;
use App\Http\Controllers\v1\ExternalApiController;

use Illuminate\Support\Facades\Route;

// API v1 routes
Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    // Protected routes - using the built-in JWT auth middleware
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);

        // Projects
        Route::post('projects/search', [ProjectController::class, 'search']); // Search projects with filters
        Route::get('projects/{id}', [ProjectController::class, 'show'])->where('id', '[0-9]+');; // Get a project details
        Route::get('projects/editors-history/{id}', [ProjectController::class, 'editorsHistory']); // Get all versions of a project
        Route::get('projects/log/{id}/log', [ProjectController::class, 'projectLog']); // Get all versions of a project
        
        // Users
        Route::apiResource('users', UserController::class); // Full CRUD operations for users

        // Enums and Views routes - with type constraint
        Route::get('{type}/{model}/{id?}', [EnumViewController::class, 'index'])
            ->where('type', 'enums|views');
        Route::post('{type}/{model}', [EnumViewController::class, 'store'])
            ->where('type', 'enums|views');
        Route::put('{type}/{model}/{id}', [EnumViewController::class, 'update'])
            ->where('type', 'enums|views');
        Route::delete('{type}/{model}/{id}', [EnumViewController::class, 'destroy'])
            ->where('type', 'enums|views');

        Route::get('external', [ExternalApiController::class, 'index']);

        });
});
