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
    /*
    |--------------------------------------------------------------------------
    | Public Routes (No Auth Required)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['guest'])->group(function () {
        Route::post('login', LoginController::class)->name('login');
        Route::post('register', RegistrationController::class)->name('register');
    });

    /*
    |--------------------------------------------------------------------------
    | Protected Routes (Auth Required)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', LogoutController::class)->name('logout');

         /*
        |--------------------------------------------------------------------------
        | Admin-Only Routes
        |--------------------------------------------------------------------------
        */
        Route::middleware(roleMiddleware(Role::Admin))->group(function () {
            Route::apiResource('users', UserController::class)->except('destroy');

            Route::get('audit-logs', [AuditLogController::class, 'index'])
                ->name('audit-logs.index');
        });

        /*
        |--------------------------------------------------------------------------
        | Expense Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('expenses')->as('expenses.')->group(function () {
            Route::get('/', [ExpenseController::class, 'index'])->name('index');
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
            Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');

            Route::put('/{expense}', [ExpenseController::class, 'update'])
                ->name('update')
                ->middleware(roleMiddleware(Role::Admin, Role::Manager));

            Route::delete('/{expense}', [ExpenseController::class, 'destroy'])
                ->name('destroy')
                ->middleware(roleMiddleware(Role::Admin));
        });
    });
});
