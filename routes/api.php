<?php

use App\Enums\Role;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\UserController;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/v1')->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest')
        ->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        Route::apiResource('users', UserController::class)
            ->only(['index', 'show', 'update', 'store'])
            ->middleware(sprintf('role:%s', Role::Admin->value));

        Route::prefix('expenses')->group(function () {
            Route::get('/', [ExpenseController::class, 'index'])
                ->name('expenses.index');
            Route::post('/', [ExpenseController::class, 'store'])
                ->name('expenses.store');
            Route::get('/{uuid}', [ExpenseController::class, 'show'])
                ->name('expenses.show');
            Route::put('/{uuid}', [ExpenseController::class, 'update'])
                ->name('expenses.update')
                ->middleware(sprintf('role:%s,%s', Role::Admin->value, Role::Manager->value));
            Route::delete('/{uuid}', [ExpenseController::class, 'destroy'])
                ->name('expenses.destroy')
                ->middleware(sprintf('role:%s', Role::Admin->value));
        });

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index')
            ->middleware(sprintf('role:%s', Role::Admin->value));
    });
});
