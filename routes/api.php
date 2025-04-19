<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureCompanyAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', EnsureCompanyAccess::class])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Expenses
    Route::apiResource('expenses', ExpenseController::class);

    // Users - Only accessible by Admins via controller authorization
    Route::apiResource('users', UserController::class)->only(['index', 'store', 'update']);
});
