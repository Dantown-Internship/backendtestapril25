<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes (Sanctum protected)
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::post('/register', [AuthController::class, 'register'])->middleware('role:Admin');

    // Expense routes
    Route::prefix('expenses')->group(function () {
        Route::get('/', [ExpenseController::class, 'index']);
        Route::post('/', [ExpenseController::class, 'store']);
        Route::put('/{expense}', [ExpenseController::class, 'update'])->middleware('role:Admin,Manager');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->middleware('role:Admin');
    });

    // User management routes (Admin only)
    Route::prefix('users')->middleware('role:Admin')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{user}', [UserController::class, 'update']);
    });
});

// Health check route (optional)
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
