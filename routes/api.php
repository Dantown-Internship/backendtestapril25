<?php

use App\Enums\Role;
use App\Http\Controllers\Api\V1\AuditLogController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\RegistrationController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->as('api.v1.')->group(function () {
    // Public routes
    Route::post('login', LoginController::class)
        ->middleware('guest')
        ->name('login');

    Route::post('register', RegistrationController::class)
        ->middleware('guest')
        ->name('register');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', LogoutController::class)->name('logout');

        // Admin-only routes
        Route::middleware(roleMiddleware(Role::Admin))->group(function () {
            Route::apiResource('users', UserController::class)
                ->only(['index', 'show', 'update', 'store']);

            Route::get('audit-logs', [AuditLogController::class, 'index'])
                ->name('audit-logs.index');
        });

        // Expenses routes with different permission levels
        Route::prefix('expenses')->as('expenses.')->group(function () {
            // Routes available to all authenticated users
            Route::get('/', [ExpenseController::class, 'index'])->name('index');
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
            Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');

            // Routes for admins and managers
            Route::middleware(roleMiddleware(Role::Admin, Role::Manager))->group(function () {
                Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
            });

            // Admin-only expense routes
            Route::middleware(roleMiddleware(Role::Admin))->group(function () {
                Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
            });
        });
    });
});
