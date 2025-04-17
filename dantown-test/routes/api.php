<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ExpensesController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/***Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

// Auth routes
Route::post('/register-user', [AuthController::class, 'registerUser'])->middleware(['auth:sanctum', 'checkRole:Admin,Manager']);
Route::post('/register-admin', [AuthController::class, 'registerAdminUser'])->middleware(['auth:sanctum','checkRole:SuperAdmin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Company route
Route::put('/update-company/{id}', [CompanyController::class, 'updateCompany'])->middleware(['auth:sanctum','checkRole:SuperAdmin']);
Route::get('/company', [CompanyController::class, 'index'])->middleware(['auth:sanctum','checkRole:SuperAdmin']);
Route::get('/company/{id}', [CompanyController::class, 'viewCompany'])->middleware(['auth:sanctum', 'checkRole:SuperAdmin,Admin,Manager']);
Route::delete('/company/{id}', [CompanyController::class, 'deleteCompany'])->middleware(['auth:sanctum', 'checkRole:SuperAdmin']);


// Expenses routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/expenses', [ExpensesController::class, 'index']);
    Route::post('/expenses', [ExpensesController::class, 'store']);
    Route::put('/expenses/{id}', [ExpensesController::class, 'update'])->middleware(['auth:sanctum', 'checkRole:Admin,Manager']);
    Route::delete('/expenses/{id}', [ExpensesController::class, 'destroy'])->middleware(['auth:sanctum', 'checkRole:Admin']);
});

// User routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
});
