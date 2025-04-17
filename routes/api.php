<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CompanyController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::post('/users', [UserController::class, 'store']);
});


Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::post('/companies', [CompanyController::class, 'store']);
});



// ✅ Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // -------------------
    // 📦 USER MANAGEMENT
    // -------------------
    Route::middleware('role:Admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
    });

    // -------------------
    // 💰 EXPENSE MANAGEMENT
    // -------------------
    // Shared by Admin & Manager
    Route::middleware('role:Admin,Manager')->group(function () {
        Route::post('/expenses', [ExpenseController::class, 'store']);
        Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
        Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
    });

    // Shared by all roles
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::get('/expenses/{id}', [ExpenseController::class, 'show']);
});
