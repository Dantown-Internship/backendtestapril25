<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AuditLogController;


Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('can:isAdmin');
    Route::post('/logout', [AuthController::class, 'logout']);


    // Expense Management
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->middleware('can:update,expense');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->middleware('can:delete,expense');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index']);

    // User Management - Admin Only
    Route::middleware('can:isAdmin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::put('/users/{user}', [UserController::class, 'update']);
    });
});
