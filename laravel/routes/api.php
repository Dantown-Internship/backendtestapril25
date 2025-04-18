<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;

Route::middleware('set-role')->middleware('set-structure')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {

        // Expense Management
        Route::get('/expenses', [ExpenseController::class, 'index'])
            ->name('expenses.index')
            ->middleware('role:Employee,Manager,Admin');

        Route::post('/expenses', [ExpenseController::class, 'store'])
            ->name('expenses.store')
            ->middleware('role:Employee,Manager,Admin');

        Route::put('/expenses/{id}', [ExpenseController::class, 'update'])
            ->name('expenses.update')
            ->middleware('role:Manager,Admin'); // Allow Managers and Admins

        Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])
            ->name('expenses.destroy')
            ->middleware('role:Admin');

        // User Management
        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index')
            ->middleware('role:Admin');

        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store')
            ->middleware('role:Admin');

        Route::put('/users/{id}', [UserController::class, 'update'])
            ->name('users.update')
            ->middleware('role:Admin');
    });

    // Authentication
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

// Authentication
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
