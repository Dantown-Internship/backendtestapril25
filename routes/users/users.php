<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController as Users;


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/create', [Users::class, 'create']);
    Route::delete('/delete/{userId}', [Users::class, 'delete']);
    Route::get('/', [Users::class, 'users']);  
    Route::get('/{userId?}', [Users::class, 'user']); 
    Route::put('/{userId}', [Users::class, 'update']);  
});
