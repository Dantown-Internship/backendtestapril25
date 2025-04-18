<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Test route
Route::get('/test', function (Request $request) {
    return response()->json(['message' => 'API routes are working!']);
});

// Public routes
Route::post('/register-company', [AuthController::class, 'registerCompany']);
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/verify', [AuthController::class, 'verify']);
    
    // Company
    Route::get('/company', [CompanyController::class, 'current']);
    Route::put('/company', [CompanyController::class, 'update']);
    
    // Expenses
    Route::apiResource('expenses', ExpenseController::class);
    
    // Users
    Route::apiResource('users', UserController::class);
});
