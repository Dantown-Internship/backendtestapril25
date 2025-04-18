<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;

/*
|
| API Routes
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Auth Routes
Route::post('/register', [AuthController::class, 'register'])->middleware(['auth:sanctum', 'role:Admin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {

    // Expense Routes
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->middleware('role:Admin,Manager');
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->middleware('role:Admin');

    // User Management Routes (Admins only)
    Route::middleware('role:Admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
    });

    // Optional: Company routes (depending on needs)
    Route::get('/companies', [CompanyController::class, 'index'])->middleware('role:Admin');
    Route::get('/companies/{id}', [CompanyController::class, 'show'])->middleware('role:Admin');

    Route::get('/test', function () {
        return response()->json(['message' => 'API is working']);
    });
});
 