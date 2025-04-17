<?php

use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Expense\Controllers\ExpenseController;
use App\Modules\User\Controllers\UserController;
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

//Authentication
Route::post('register', [AuthController::class, 'store'])->name('register.user');
Route::post('login', [AuthController::class, 'login'])->name('login.user');


Route::middleware('auth:sanctum')->group(function () {
    //Users
    Route::prefix('users')->group(function () {
        Route::post('', [UserController::class, 'create'])->name('store.user');
        Route::get('', [UserController::class, 'index'])->name('get.users');
        Route::put('/{id}', [UserController::class, 'edit'])->name('update.user');
    });

    //Expenses
    Route::prefix('expenses')->group(function (){
        Route::post('',[ExpenseController::class, 'store'])->name('store.expense');
        Route::get('', [ExpenseController::class, 'index'])->name('get.expenses');
        Route::put('{expense}', [ExpenseController::class, 'edit'])->name('update.expense');
        Route::delete('{expense}', [ExpenseController::class, 'destroy'])->name('destroy.expense');
    })->middleware('user.belongs.to.company');
});
