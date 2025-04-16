<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AllowAdminOrManager;
use App\Http\Middleware\AllowAdminRole;
use App\Http\Middleware\ApiAuth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to the Expense Management API',
        'version' => '1.0.0',
    ]);
})->name('api.index');

Route::post('login', [AuthController::class, 'login']);

Route::middleware([ApiAuth::class])->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('expenses', [ExpenseController::class, 'index']);
    Route::post('expenses', [ExpenseController::class, 'store']);

    Route::put('expenses/{id}', [ExpenseController::class, 'update'])->middleware([AllowAdminOrManager::class]);

    Route::middleware([AllowAdminRole::class])->group(function () {
        Route::apiResource('companies', CompanyController::class);

        Route::post('register', [AuthController::class, 'register']);
        Route::delete('expenses/{id}', [ExpenseController::class, 'destroy']);

        Route::get('users', [UserController::class, 'index']);
        Route::post('users', [AuthController::class, 'register']);
        Route::put('users/{id}', [UserController::class, 'update']);
    });
});
