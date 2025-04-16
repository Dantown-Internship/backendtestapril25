<?php

use App\Http\Controllers\Api\AuditController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Authentication
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Expenses
Route::apiResource('expenses', ExpenseController::class)->middleware('auth:sanctum');

// Users Management
Route::apiResource('users', UserController::class)->middleware(['auth:sanctum', 'role:admin']);

// Audit Logs
Route::apiResource('audits', AuditController::class)->middleware(['auth:sanctum', 'role:admin']);
