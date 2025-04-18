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
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware(['checkRole:Admin'])->group(function () {
        Route::post('/register-user', [AuthController::class, 'registerUser']);
    });
    Route::middleware(['checkRole:SuperAdmin'])->group(function () {
        Route::post('/register-admin', [AuthController::class, 'registerAdminUser']);
    });
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Company routes
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware(['checkRole:SuperAdmin'])->group(function () {
        Route::get('/companies', [CompanyController::class, 'index']);
        Route::put('/update-company/{id}', [CompanyController::class, 'updateCompany']);
        Route::patch('/deactivate-company/{id}', [CompanyController::class, 'deactivateCompany']);
        Route::patch('/activate-company/{id}', [CompanyController::class, 'activateCompany']);
        Route::delete('/company/{id}', [CompanyController::class, 'deleteCompany']);
    });

    Route::middleware(['checkRole:Admin,Manager'])->group(function () {
        Route::get('/company', [CompanyController::class, 'viewCompany']);
    });
});

// Expenses routes
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware(['checkRole:Admin,Manager,Employee'])->group(function () {
        Route::get('/expenses', [ExpensesController::class, 'index']);
        Route::get('/expenses/{id}', [ExpensesController::class, 'show']);
        Route::post('/expenses', [ExpensesController::class, 'store']);
    });

    Route::middleware(['checkRole:Admin,Manager'])->group(function () {
        Route::put('/expenses/{id}', [ExpensesController::class, 'update']);
    });

    Route::middleware(['checkRole:Admin'])->group(function () {
        Route::delete('/expenses/{id}', [ExpensesController::class, 'destroy']);
    });
});

// User routes
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware(['checkRole:Admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'viewUser']);
        Route::put('/users/{id}', [UserController::class, 'updateUser']);
        Route::delete('/users/{id}', [UserController::class, 'deleteUser']);
    });

    Route::put('/users/{id}', [UserController::class, 'updateUserPassword']);
    
});
