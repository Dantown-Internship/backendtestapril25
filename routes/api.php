<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpenseController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/register', [AuthController::class, 'register'])->middleware('role:admin');

    // User management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
    });

    // Expense management
    Route::prefix('expenses')->group(function () {
        // All authenticated users can view and create expenses
        Route::get('/', [ExpenseController::class, 'index']);
        Route::post('/', [ExpenseController::class, 'store']);

        // Only managers and admins can update expenses
        Route::middleware('role:manager')->group(function () {
            Route::put('/{expense}', [ExpenseController::class, 'update']);
        });

        // Only admins can delete expenses
        Route::middleware('role:admin')->group(function () {
            Route::delete('/{expense}', [ExpenseController::class, 'destroy']);
        });
    });
}); 