<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\kelas\KelasController;
use App\Http\Controllers\Api\KitabSurahController;
use App\Http\Controllers\Api\MutabaahController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SetoranController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::prefix('auth')->group(function () {
    // Public routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

// Roles Management
Route::apiResource('roles', RoleController::class);

// Protected Routes
Route::middleware('auth:api')->group(function () {
    // User Management Routes
    Route::prefix('users')->group(function () {
        // Admin & Super Admin only routes
        Route::middleware('role:3,4')->group(function () {
            Route::post('/', [UserController::class, 'store']);
            Route::put('/{user}', [UserController::class, 'update']);
        });

        // Ustad, Admin & Super Admin routes
        Route::middleware('role:2,3,4')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/{user}', [UserController::class, 'show']);
        });
    });

    // Educational Content Routes
    Route::prefix('educational')->group(function () {
        // Resources accessible to all authenticated users
        Route::prefix('kelas')->group(function () {
            Route::get('/', [KelasController::class, 'index']);
            Route::get('/{kelas}', [KelasController::class, 'show']);

            // Ustad, Admin & Super Admin operations
            Route::middleware('role:2,3,4')->group(function () {
                Route::post('/', [KelasController::class, 'store']);
                Route::put('/{kelas}', [KelasController::class, 'update']);
                Route::patch('/{kelas}', [KelasController::class, 'update']);
            });

            // Super Admin only operations
            Route::middleware('role:4')->group(function () {
                Route::delete('/{kelas}', [KelasController::class, 'destroy']);
            });
        });

        // Kitab Surah routes
        Route::prefix('kitab-surah')->group(function () {
            Route::get('/', [KitabSurahController::class, 'index']);
            Route::get('/{id}', [KitabSurahController::class, 'show']);

            Route::middleware('role:2,3,4')->group(function () {
                Route::post('/', [KitabSurahController::class, 'store']);
                Route::put('/{id}', [KitabSurahController::class, 'update']);
                Route::patch('/{id}', [KitabSurahController::class, 'update']);
            });

            Route::middleware('role:4')->group(function () {
                Route::delete('/{id}', [KitabSurahController::class, 'destroy']);
            });
        });
    });

    // Activity Management Routes
        // Setoran routes
        Route::prefix('jenis-setoran')->group(function () {
            Route::get('/', [SetoranController::class, 'index']);
            Route::get('/{id}', [SetoranController::class, 'show']);

            Route::middleware('role:2,3,4')->group(function () {
                Route::post('/', [SetoranController::class, 'store']);
                Route::put('/{id}', [SetoranController::class, 'update']);
                Route::patch('/{id}', [SetoranController::class, 'update']);
            });

            Route::middleware('role:4')->group(function () {
                Route::delete('/{id}', [SetoranController::class, 'destroy']);
            });
        });

        // Mutabaah routes
        Route::prefix('mutabaah')->group(function () {
            Route::get('/', [MutabaahController::class, 'index']);
            Route::get('/{id}', [MutabaahController::class, 'show']);

            Route::middleware('role:2,3,4')->group(function () {
                Route::post('/', [MutabaahController::class, 'store']);
                Route::put('/{id}', [MutabaahController::class, 'update']);
                Route::patch('/{id}', [MutabaahController::class, 'update']);
            });

            Route::middleware('role:4')->group(function () {
                Route::delete('/{id}', [MutabaahController::class, 'destroy']);
            });
        });
    });
