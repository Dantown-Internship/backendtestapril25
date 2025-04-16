<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::prefix('expenses')->middleware(['auth:sanctum'])->group(function () {
    // GET /expenses - Any authenticated user can list expenses
    Route::get('/', [ExpenseController::class, 'index']);
    
    // POST /expenses - Any authenticated user can create
    Route::post('/', [ExpenseController::class, 'store']);
    
    // PUT /expenses/{expense} - Only managers/admins from same company
    Route::put('/{expense}', [ExpenseController::class, 'update'])->middleware('can:update,expense');
    
    // DELETE /expenses/{expense} - Only admins from same company
    Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->middleware('can:delete,expense');
});

// Only admins from same company
Route::prefix('user')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [UserController::class, 'view']);
    
    Route::post('/', [UserController::class, 'create']);
    
    Route::put('/{user}', [UserController::class, 'update'])->middleware('can:update,user');
});
Route::get('/audit-log', [AuditLogController::class, 'index']);


Route::get('/unauthorised_response', function () {
    return response()->json(['message' => 'Unauthenticated. Please log in.'], 401);
})->name('login');
