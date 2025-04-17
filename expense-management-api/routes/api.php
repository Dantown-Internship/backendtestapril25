<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\UserController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Admin Role Middleware (Manage Users)
    Route::middleware('role:Admin')->group(function () {
        // User Management (Admins only)
        Route::apiResource('users', UserController::class)->except(['show']); // Admins can manage users
        Route::post('users/{user}/update-role', [UserController::class, 'updateRole']);
    });

    // Admin and Manager Role Middleware (Manage Expenses)
    Route::middleware('role:Admin,Manager')->group(function () {
        // Expense Management (Admins and Managers only)
        Route::apiResource('expenses', ExpenseController::class)->except(['index', 'show']); // Exclude index/show for now
    });

    // Admin, Manager, and Employee Role Middleware (View and Create Expenses)
    Route::middleware('role:Admin,Manager,Employee')->group(function () {
        // View and Create Expenses (accessible to Admins, Managers, and Employees)
        Route::get('expenses', [ExpenseController::class, 'index']); // List expenses (paginated, searchable by title/category)
        Route::get('expenses/{expense}', [ExpenseController::class, 'show']); // Show a specific expense
        Route::post('expenses', [ExpenseController::class, 'store']); // Create a new expense (restricted to the user's company)
    });
});

// Authentication Routes (Available to everyone)
Route::post('register', [AuthController::class, 'register'])->middleware('role:Admin'); // Admin only can register new users
Route::post('login', [AuthController::class, 'login']); // Everyone can login

// User Details Routes (Available to authenticated users)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
});

// Password Reset Routes (Available to everyone)
Route::post('password/email', [AuthController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [AuthController::class, 'reset']);
Route::post('password/confirm', [AuthController::class, 'confirmPassword']);
// Route::post('password/confirm', [AuthController::class, 'confirmPassword']);
// Route::post('password/confirm', [AuthController::class, 'confirmPassword']);
// Route::post('password/confirm', [AuthController::class, 'confirmPassword']);
// Route::post('password/confirm', [AuthController::class, 'confirmPassword']);
// Route::post('password/confirm', [AuthController::class, 'confirmPassword']);
// Route::post('password/confirm', [AuthController::class, 'confirmPassword']); 
