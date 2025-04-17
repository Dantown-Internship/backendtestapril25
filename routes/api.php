<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;


Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});

// Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
//     Route::get('/users', [UserController::class, 'getUsers']);
// });

Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::put('/users/{user}', [UserController::class, 'update']);
});

Route::get('/health-check', [HealthCheckController::class, 'healthCheck']);


Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
});
Route::get('/expenses', [ExpenseController::class, 'index'])
    ->middleware('auth:sanctum');

Route::post('/expenses', [ExpenseController::class, 'store'])
    ->middleware('auth:sanctum');


Route::middleware(['auth:sanctum', 'company'])->group(function () {
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])
            ->middleware('role:Admin,Manager');
    });

Route::middleware(['auth:sanctum', 'company'])->group(function () {
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])
            ->middleware('role:Admin');
    });

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');


