<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $audit_logs = AuditLog::with('user')
                ->where('company_id', auth()->user()->company_id)
                ->when(
                    $request->search,
                    fn($q) =>
                    $q->where('action', 'like', "%$request->search%")
                )
                ->paginate($request->limit ?? 10);

                return ResponseHelper::success($audit_logs, 'Audit logs fetched successfully', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to fetch audit logs', ['error' => $e->getMessage()], 500);
        }
    }

}
