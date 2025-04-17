<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function companyLogs($companyId)
    {
        $logs = AuditLog::where('company_id', $companyId)->get();
        return response()->json($logs);
    }

    public function userLogs($userId)
    {
        $logs = AuditLog::where('user_id', $userId)->get();
        return response()->json($logs);
    }
}
