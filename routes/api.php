<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\NewUserController;
use App\Http\Controllers\ExpenseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::Post("/login",[AdminAuthController::class,"login"])->name("admin.login");
Route::middleware(['auth:sanctum', 'role'])
->controller(AdminAuthController::class)
->group(function() {
   Route::Post("/register","AdminRegister")->name("admin.register");
  
   Route::Post("/create_user","create_user")->name('user.create');
});

//THIS ROUTE IS FOR USER MANAGEMENT
Route::middleware(['auth:sanctum','userMgt'])
->controller(NewUserController::class)
->group(function() {
 Route::Post("/create_user","create_user")->name("user.create");
 Route::Post('/logout', 'logout')->name('users.logout');
 Route::Get("view_users","view_users")->name("user.view");
 Route::Put("update_user/{id}","update_user")->name("user.update");
});

//This Route Is For Expense Management
Route::middleware(["auth:sanctum"])
->controller(ExpenseController::class)
->group(function() {
  Route::Post("/create_expense","create")->name("expense.create");
  Route::Get("/view_expense","view")->name("expense.view");
  Route::Put("/update_expense/{expense}","update")->name("expense.update");
  Route::Delete("/delete_expense/{expense}","delete")->name("expense.delete");
});