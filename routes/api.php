<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])
    ->middleware(['auth:sanctum', 'role:Admin', 'throttle:api']);

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:login');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Log::info('Request Headers:', request()->headers->all());

    Route::middleware('role:Admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });

    Route::middleware('role:Manager,Admin')->group(function () {
        Route::get('/expenses', [ExpenseController::class, 'index']);
        Route::post('/expenses', [ExpenseController::class, 'store']);
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update']);
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy']);
    });

    Route::middleware('role:Employee,Manager,Admin')->group(function () {
        Route::get('/employee/expenses', [ExpenseController::class, 'employeeExpenses']);
        Route::post('/employee/expenses', [ExpenseController::class, 'employeeStore']);
    });
});

//TESTS


Route::get('/throttle-check', function () {
    return response()->json([
        'message' => 'Still good!',
    ]);
})->middleware('throttle:api');
