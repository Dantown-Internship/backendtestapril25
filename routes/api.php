<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');



Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::get('users', [UserController::class, 'getUsers']);
    Route::post('users', [UserController::class, 'addUser']);
    Route::put('users/{id}', [UserController::class, 'updateUser']);
    Route::get('audits', [AuditLogController::class, 'index']);
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('expenses', ExpenseController::class);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
});


Route::fallback(function (Request $request) {
    return response()->json([
        'message' => 'Endpoint not found',
        'endpoint' => $request->path(),
    ], 404);
});
