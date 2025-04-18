<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs.
     *
     * @group Audit Logs
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $query = AuditLog::query()
            ->where('company_id', $request->user()->company_id)
            ->with(['user:id,name,email']);
        
        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by action type
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }
        
        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Order by latest first
        $query->latest();
        
        return $query->paginate(15);
    }
    
    /**
     * Display the specified audit log.
     *
     * @group Audit Logs
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Check if user is admin
        if ($request->user()->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $auditLog = AuditLog::with('user:id,name,email')
            ->where('company_id', $request->user()->company_id)
            ->findOrFail($id);
        
        return response()->json($auditLog);
    }
} 