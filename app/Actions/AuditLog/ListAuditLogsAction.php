<?php

namespace App\Actions\AuditLog;

use App\Models\AuditLog;

class ListAuditLogsAction
{
    public function __construct(
        private AuditLog $auditLog
    ) {}

    public function execute(array $listAuditLogRecordOptions, array $relationships = [])
    {
        $paginationPayload = $listAuditLogRecordOptions['pagination_payload'] ?? null;
        $filterRecordOptionsPayload = $listAuditLogRecordOptions['filter_record_options_payload'] ?? null;

        $query = $this->auditLog->query()
            ->with($relationships)
            ->orderBy('created_at', 'desc');

        if (!empty($filterRecordOptionsPayload['company_id'])) {
            $query->where('company_id', $filterRecordOptionsPayload['company_id']);
        }

        if (!empty($filterRecordOptionsPayload['user_id'])) {
            $query->where('user_id', $filterRecordOptionsPayload['user_id']);
        }

        if (!empty($filterRecordOptionsPayload['search_query'])) {
            $query->where('action', 'LIKE', $filterRecordOptionsPayload['search_query'] . '%');
        }

        if ($paginationPayload) {
            $paginatedAuditLogs = $query->paginate(
                $paginationPayload['limit'] ?? config('businessConfig.default_page_limit'),
                ['*'],
                'page',
                $paginationPayload['page'] ?? 1
            );

            return [
                'audit_log_payload' => $paginatedAuditLogs->items(),
                'pagination_payload' => [
                    'meta' => generatePaginationMeta($paginatedAuditLogs),
                    'links' => generatePaginationLinks($paginatedAuditLogs)
                ],
            ];
        }

        $auditLogs = $query->get();

        return [
            'audit_log_payload' => $auditLogs,
        ];
    }
}
