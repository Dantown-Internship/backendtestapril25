<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    //view audit log
    public function index()
    {
        return response()->json(
            AuditLog::with('user')
                ->where('company_id', auth()->user()->company_id)
                ->latest()
                ->get()
        );
    }
}
