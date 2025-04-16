<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditLogIndexRequest;
use App\Models\AuditLog;
use App\Models\Company;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * AuditLog Controller
 *
 * Handles all operations related to audit logs within the system.
 * Provides endpoints for viewing and filtering audit logs.
 * All operations are scoped to the authenticated user's company.
 *
 * @group Audit Logs
 */
class AuditLogController extends Controller
{
    use JsonResponseTrait;

    /**
     * Display a paginated list of audit logs.
     *
     * Returns a list of audit logs for the authenticated user's company.
     *
     * @queryParam search string Search in action or model_type. Example: create
     * @queryParam start_date string Filter by start date. Example: 2024-01-01
     * @queryParam end_date string Filter by end date. Example: 2024-12-31
     * @queryParam user_id int Filter by user ID. Example: 1
     * @queryParam model_type string Filter by model type. Example: App\\Models\\Expense
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user_id": 1,
     *       "company_id": 1,
     *       "action": "create",
     *       "changes": {"old": {}, "new": {"title": "Test"}},
     *       "model_type": "App\\Models\\Expense",
     *       "model_id": 1,
     *       "created_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "per_page": 15,
     *     "total": 100
     *   }
     * }
     */
    public function index(AuditLogIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $companyId = Auth::user()->company_id;
        $cacheKey = "audit_logs:company:{$companyId}:page:{$validated['page']}:per_page:{$validated['per_page']}";

        if (isset($validated['search'])) {
            $cacheKey .= ":search:{$validated['search']}";
        }
        if (isset($validated['start_date'])) {
            $cacheKey .= ":start_date:{$validated['start_date']}";
        }
        if (isset($validated['end_date'])) {
            $cacheKey .= ":end_date:{$validated['end_date']}";
        }
        if (isset($validated['action'])) {
            $cacheKey .= ":action:{$validated['action']}";
        }

        $logs = Cache::remember($cacheKey, 3600, function () use ($validated, $companyId) {
            $query = AuditLog::where('company_id', $companyId);

            if (isset($validated['search'])) {
                $search = $validated['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                        ->orWhere('model_type', 'like', "%{$search}%")
                        ->orWhere('user_id', 'like', "%{$search}%");
                });
            }

            if (isset($validated['start_date'])) {
                $query->whereDate('created_at', '>=', $validated['start_date']);
            }

            if (isset($validated['end_date'])) {
                $query->whereDate('created_at', '<=', $validated['end_date']);
            }

            if (isset($validated['action'])) {
                $query->where('action', $validated['action']);
            }

            return $query->paginate($validated['per_page'], ['*'], 'page', $validated['page']);
        });

        return $this->successResponse($logs, 'Audit logs retrieved successfully');
    }

    /**
     * Display the specified audit log.
     *
     * @param AuditLog $auditLog
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(AuditLog $auditLog): JsonResponse
    {
        if ($auditLog->company_id !== Auth::user()->company_id) {
            return $this->forbiddenResponse('You do not have permission to view this audit log');
        }

        return $this->successResponse($auditLog, 'Audit log retrieved successfully');
    }

    /**
     * Clear the audit logs cache for a company.
     *
     * Clears all cached audit log queries for the specified company.
     * Only accessible by company admins.
     *
     * @urlParam company int required The ID of the company. Example: 1
     *
     * @response 200 {
     *   "message": "Audit logs cache cleared successfully"
     * }
     */
    public function clearCompanyAuditLogsCache(int $companyId): JsonResponse
    {
        $company = Company::findOrFail($companyId);

        if ($company->id !== Auth::user()->company_id) {
            return $this->notFoundResponse('Company not found');
        }

        Cache::tags(['audit_logs'])->flush();

        return $this->successMessage('Audit logs cache cleared successfully');
    }
}
