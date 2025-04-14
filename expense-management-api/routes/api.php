<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    // Route::apiResource('expenses', ExpenseController::class);
    // Route::apiResource('users', UserController::class);
Route::apiResource('companies', CompanyController::class);

});