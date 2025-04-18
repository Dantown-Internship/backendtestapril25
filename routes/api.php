<?php

use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;

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
Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated'], 401);
})->name('login');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store'])->middleware('role:Employee,Manager,Admin');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->middleware('role:Manager,Admin');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->middleware('role:Admin');

    Route::get('/users', [UserController::class, 'index'])->middleware('role:Admin');
    Route::post('/users', [UserController::class, 'store'])->middleware('role:Admin');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('role:Admin');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->middleware('role:Admin');
});