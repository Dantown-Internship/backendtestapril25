<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/login', LoginController::class)->name('login');

// Protected routes
Route::middleware(['auth:sanctum', 'same-company'])->group(function () {
    Route::post('/register', RegisterController::class)->name('register');
    Route::apiResource('users', UserController::class);
    Route::apiResource('companies', CompanyController::class)->only(['index', 'show', 'update']);
    Route::apiResource('expenses', ExpenseController::class);
});

