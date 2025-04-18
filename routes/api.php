// routes/api.php
<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth:sanctum', 'company.scope'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // User management (Admin only)
    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
    });

    // Expense management
    Route::get('/expenses', [ExpenseController::class, 'index']); // All roles
    Route::post('/expenses', [ExpenseController::class, 'store']); // All roles

    // Expense management (Managers & Admins only)
    Route::middleware(['role:Admin,Manager'])->group(function () {
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update']);
    });

    // Expense management (Admin only)
    Route::middleware(['role:Admin'])->group(function () {
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy']);
    });
});