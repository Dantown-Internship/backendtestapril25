<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;
use App\Models\Expense;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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

// Explicit model binding for Expense
Route::bind('expense', function ($value) {
    $expense = Expense::find($value);
    if (!$expense) {
        abort(404, 'Expense not found');
    }
    return $expense;
});

// Auth Routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout')->name('logout');
    });
});

// Protected Routes
Route::middleware(['auth:sanctum', 'company.scope'])->group(function () {
    // Admin Routes
    Route::middleware('admin')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });

    // Expense Routes
    Route::controller(ExpenseController::class)->prefix('expenses')->group(function () {
        Route::get('/', 'index')->name('expenses.index');
        Route::post('/', 'store')->name('expenses.store');
        Route::get('/summary', 'summary')->name('expenses.summary');
        Route::get('/{expense}', 'show')->name('expenses.show');
        Route::put('/{expense}', 'update')->name('expenses.update');
        Route::delete('/{expense}', 'destroy')->name('expenses.destroy');
    });

    // User Routes
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::put('/password', 'updatePassword')->name('users.updatePassword');
        Route::put('/{user}', 'update')->name('users.update');
    });

    // Audit Log Routes
    Route::controller(AuditLogController::class)->prefix('audit-logs')->group(function () {
        Route::get('/', 'index')->name('audit-logs.index');
        Route::get('/{auditLog}', 'show')->name('audit-logs.show');
        Route::post('/{company}/clear-cache', 'clearCompanyAuditLogsCache')->name('audit-logs.clear-cache');
    });
});
