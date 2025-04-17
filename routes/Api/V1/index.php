<?php

use Illuminate\Support\Facades\Route;

Route::prefix('authentication')->group(__DIR__.'/AuthenticationRoutes.php');
Route::prefix('user-management')->group(__DIR__.'/UserManagementRoutes.php');
Route::prefix('expense-management')->group(__DIR__.'/ExpenseManagementRoutes.php');
Route::prefix('audit-log-management')->group(__DIR__.'/AuditLogRoutes.php');
Route::prefix('settings')->group(__DIR__.'/SettingsRoutes.php');
