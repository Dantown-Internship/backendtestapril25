<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\UserController;


// Public route for login.
Route::post('/login', [AuthController::class, 'loginUser'])->name('login');

// Routes that require authentication.
Route::middleware('auth:sanctum')->group(function () {

    // Logout route.
    Route::post('/logout', [AuthController::class, 'logoutUser'])->name('logout');

    // Admins only route
    Route::middleware('checkRole:' . \App\Enums\UserRole::Admin->value)->group(function () {

        // Registration route
        Route::post('/register', [AuthController::class, 'registerUser'])->name('register');

        // Expense route (delete action)
        Route::delete('/expenses/{id}', [ExpenseController::class, 'deleteExpense'])->name('expense.delete');

        // User Management route.
        Route::get('/users', [UserController::class, 'getUserList'])->name('admin-user.list');
        Route::post('/users', [UserController::class, 'createUser'])->name('admin-user.create');
        Route::put('/users/{id}', [UserController::class, 'updateUserRole'])->name('admin-user.updateRole');
    });

    // Expense Management Endpoints.
    Route::get('/expenses', [ExpenseController::class, 'getExpenseList'])->name('expense.list');
    Route::post('/expenses', [ExpenseController::class, 'createExpense'])->name('expense.create');
    Route::put('/expenses/{id}', [ExpenseController::class, 'updateExpense'])
    ->middleware('checkRole:' . \App\Enums\UserRole::Admin->value . ',' . \App\Enums\UserRole::Manager->value)->name('expense.update');
    
});


