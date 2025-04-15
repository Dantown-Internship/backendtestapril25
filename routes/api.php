<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Middleware\AdminOrManager;
use App\Http\Middleware\CheckAdminLogin;
use App\Http\Middleware\RoleMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes (Laravel Sanctum Protected)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware(['guest', CheckAdminLogin::class]);


Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');


Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);

// Routes grouped under Sanctum (All roles)
Route::middleware('auth:sanctum')->group(function () {
    /**
    * Expense Routes
    */
    Route::get('/expenses', [ExpenseController::class, 'index']);

    /**
    * Admin only routes
    */
    Route::middleware(['auth:sanctum', RoleMiddleware::class])->group(function () {
        Route::get('/users', [UserController::class, 'index']); //List users
        Route::post('/users', [UserController::class, 'store']); //Admin only: register new user
        Route::put('/users/{user}/role', [UserController::class, 'updateRole']); // Update role
        Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
    });


    /**
    * Admin and Manager Routes Only
    */
    Route::middleware(['auth:sanctum', AdminOrManager::class])->group(function () {
        Route::post('/expenses', [ExpenseController::class, 'store']);
        Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
    });
     
});