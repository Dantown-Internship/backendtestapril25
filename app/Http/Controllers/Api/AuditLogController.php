<?php

namespace App\Http\Controllers\Api;

use App\Enums\AuditLogAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListAuditLogsRequest;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Support\Carbon;

class AuditLogController extends Controller
{
    public function index(ListAuditLogsRequest $request)
    {
        $perPage = $request->validated('per_page', 10) ?? 10;
        $action = AuditLogAction::tryFrom($request->validated('action'));
        $fromDate = $request->validated('from_date');
        $toDate = $request->validated('to_date');
        $query = AuditLog::query()
            ->with('user')
            ->when($action, function ($query) use ($action) {
                $query->where('action', $action);
            })
            ->when($fromDate, function ($query) use ($fromDate) {
                $query->where('created_at', '>=', Carbon::parse($fromDate)->startOfDay());
            })
            ->when($toDate, function ($query) use ($toDate) {
                $query->where('created_at', '<=', Carbon::parse($toDate)->endOfDay());
            });
        $auditLogs = $query->latest()->paginate($perPage)->withQueryString();

        return $this->paginatedResponse(
            message: 'Audit logs retrieved successfully.',
            data: AuditLogResource::collection($auditLogs)
        );
    }
}
