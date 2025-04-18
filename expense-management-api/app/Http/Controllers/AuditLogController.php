<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return AuditLog::with('user')
            ->where('company_id', $user->company_id)
            ->latest()
            ->paginate(20);
    }
}
