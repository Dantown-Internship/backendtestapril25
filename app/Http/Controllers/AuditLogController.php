<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
{
    return AuditLog::where('company_id', auth()->user()->company_id)
        ->latest()
        ->paginate(20);
}
}
