<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::middleware(['auth:sanctum', 'cache_response'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('auth.logout');

    Route::apiResource('/expenses', ExpenseController::class)
        ->except('show')
        ->names('expenses');

    Route::apiResource('/users', UserController::class)
        ->except(['show', 'destroy'])
        ->names('users');
});
