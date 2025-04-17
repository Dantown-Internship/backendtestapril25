<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ExpenseController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => '/expenses',
    'middleware' => ['auth:sanctum']
], function () {
    Route::get('/', [ExpenseController::class, 'index']);
    Route::post('/', [ExpenseController::class, 'store']);
    Route::put('/{id}', [ExpenseController::class, 'single'])->middleware('manager.auth');
    Route::delete('/{id}', [ExpenseController::class, 'SoftDeletes'])->middleware('admin.auth');
});

Route::group([
    'prefix' => '/users',
    'middleware' => ['auth:sanctum', 'admin.auth']
], function () {
    Route::get('/', [AuthController::class, 'index']);
    Route::post('/', [AuthController::class, 'addUser']);
    Route::put('/{id}', [AuthController::class, 'updateUser']);
});
Route::group([
    'prefix' => '/audit',
    'middleware' => ['auth:sanctum']
], function () {
    Route::get('/', [AuditController::class, 'index'])->middleware('admin.auth');
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
