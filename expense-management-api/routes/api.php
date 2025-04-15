<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AllowAdminRole;
use App\Http\Middleware\ApiAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to the Expense Management API',
        'version' => '1.0.0',
    ]);
})->name('api.index');

Route::post('login', [AuthController::class, 'login']);
Route::middleware([ApiAuth::class])->group(function () {
    // Route::apiResource('expenses', ExpenseController::class);
    // Route::apiResource('users', UserController::class);

    Route::apiResource('companies', CompanyController::class);

    Route::middleware([AllowAdminRole::class])->group(function () {
    Route::post('register', [AuthController::class, 'register']);
        
    });
});
