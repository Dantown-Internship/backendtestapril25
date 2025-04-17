<?php

namespace App\Http\Controllers\V1\AuditLogs;

use App\Actions\AuditLog\ListAuditLogsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AuditLogs\FetchAuditLogsRequest;
use App\Http\Resources\V1\AuditLogs\FetchAuditLogsResource;

class FetchAuditLogsController extends Controller
{
    public function __construct(
        private ListAuditLogsAction $listAuditLogsAction
    ) {}

    public function __invoke(FetchAuditLogsRequest $request)
    {
        $loggedInUser = auth('sanctum')->user();

        $relationships = ['user'];
        ['audit_log_payload' => $auditLogs, 'pagination_payload' => $paginationPayload] = $this->listAuditLogsAction->execute([
            'filter_record_options_payload' => [
                'company_id' => $loggedInUser->company_id,
                'user_id' => $request->user_id ?? null,
                'search_query' => $request->search_query,
            ],
            'pagination_payload' => [
                'page' => $request->page ?? 1,
                'limit' => $request->per_page ?? 20,
            ]
        ], $relationships);

        $mutatedAuditLogs = FetchAuditLogsResource::collection($auditLogs);

        $responsePayload = [
            'audit_logs' => $mutatedAuditLogs,
            'pagination_payload' => $paginationPayload
        ];

        return generateSuccessApiMessage('The list of audit logs were retrieved successfully', 200, $responsePayload);
    }
}
