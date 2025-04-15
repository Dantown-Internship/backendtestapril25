<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth routes
Route::post('/register', [AuthController::class, 'register']); // Admin Only
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::post('/register-company', [CompanyController::class, 'registerCompany']); // Super Admin Only
Route::post('/update-company', [CompanyController::class, 'updateCompany']); // Super Admin Only
Route::post('/company', [CompanyController::class, 'index']); // Super Admin Only
Route::post('/company/{id}', [CompanyController::class, 'viewCompany']); // Super Admin Only


// Expenses routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->middleware('role:Admin|Manager');
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->middleware('role:Admin');
});


Route::middleware('auth:sanctum', 'role:Admin')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
});
