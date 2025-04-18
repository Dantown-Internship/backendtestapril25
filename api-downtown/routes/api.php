<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Routes for user authentication, including registration, login, logout,
| email verification, password management, and token refresh.
|
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.reset');
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');

// Protected authentication routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::put('/password', [AuthController::class, 'updatePassword']); 
});

/*
|--------------------------------------------------------------------------
| Expense Management Routes
|--------------------------------------------------------------------------
|
| Routes for managing expenses, accessible to authenticated users with
| role-based restrictions (Admin, Manager, Employee).
|
*/
Route::middleware('auth:sanctum', 'token')->group(function () {
    Route::post('/save/expenses', [ExpensesController::class, 'saveExpenses']);
    Route::get('/expenses', [ExpensesController::class, 'listExpenses']);
    Route::put('/expenses/{id}', [ExpensesController::class, 'updateExpenses'])->middleware('role:Admin,Manager');
    Route::delete('/expenses/{id}', [ExpensesController::class, 'destroyExpenses'])->middleware('role:Admin');
});


/*
|--------------------------------------------------------------------------
| User Management Routes
|--------------------------------------------------------------------------
|
| Routes for managing users, restricted to Admins only.
|
*/
Route::middleware(['role:Admin', 'auth:sanctum', 'token'])->group(function () {
    Route::get('/list/users', [UserController::class, 'listUsers']);
    Route::post('/users', [UserController::class, 'storeUsersData']);
    Route::put('/user/{id}', [UserController::class, 'updateRole']);
});