<?php

use App\Http\Controllers\V1\AuditLogs\FetchAuditLogsController;
use App\Http\Controllers\V1\AuditLogs\GetAuditLogController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:sanctum']
], function () {

    Route::get('/audit-logs/{expenseId}', GetAuditLogController::class);

    Route::get('/audit-logs', FetchAuditLogsController::class);
});
