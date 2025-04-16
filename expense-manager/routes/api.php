<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Authentication
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Expenses
Route::apiResource('expenses', ExpenseController::class)->middleware('auth:sanctum');

// Users Management
Route::apiResource('users', UserController::class)->middleware(['auth:sanctum','role:admin']);
