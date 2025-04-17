<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckUserRole;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\UserIsInCompany;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AuditLogController;

// Unauthenticated root route
Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is running',
        'version' => 'v1',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register-user', [AuthController::class, 'register_user'])
    ->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Protected routes
Route::middleware(['auth:sanctum', UserIsInCompany::class])->group(function () {
    // Expense routes with role checks
    Route::apiResource('expenses', ExpenseController::class)
        ->middleware(CheckUserRole::class . ':Admin,Manager,Employee');

    // User management routes (admin only)
    Route::apiResource('users', UserController::class)
        ->middleware(CheckUserRole::class . ':Admin')
        ->except(['destroy']);
});

// Audit logs (admin only)
Route::get('/audit-logs', [AuditLogController::class, 'index'])
    ->middleware(['auth:sanctum', UserIsInCompany::class, CheckUserRole::class . ':Admin']);
