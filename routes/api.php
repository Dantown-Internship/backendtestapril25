<?php

use App\Enums\Role;
use App\Http\Controllers\Api\V1\AuditLogController;
use App\Http\Controllers\Api\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/v1')->as('api.v1.')->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest')
        ->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        Route::apiResource('users', UserController::class)
            ->only(['index', 'show', 'update', 'store'])
            ->middleware(sprintf('role:%s', Role::Admin->value));

        Route::prefix('expenses')->as('expenses.')->group(function () {
            Route::get('/', [ExpenseController::class, 'index'])
                ->name('index');
            Route::post('/', [ExpenseController::class, 'store'])
                ->name('store');
            Route::get('/{uuid}', [ExpenseController::class, 'show'])
                ->name('show');
            Route::put('/{uuid}', [ExpenseController::class, 'update'])
                ->name('update')
                ->middleware(sprintf('role:%s,%s', Role::Admin->value, Role::Manager->value));
            Route::delete('/{uuid}', [ExpenseController::class, 'destroy'])
                ->name('destroy')
                ->middleware(sprintf('role:%s', Role::Admin->value));
        });

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index')
            ->middleware(sprintf('role:%s', Role::Admin->value));
    });
});
