<?php

namespace App\Http\Controllers\V1\AuditLogs;

use App\Actions\AuditLog\GetAuditLogByIdAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AuditLogs\GetAuditLogResource;

class GetAuditLogController extends Controller
{
    public function __construct(
        private GetAuditLogByIdAction $getAuditLogByIdAction
    ) {}

    public function __invoke(string $auditLogId)
    {
        $loggedInUser = auth('sanctum')->user();

        $relationships = ['user'];
        $auditLog = $this->getAuditLogByIdAction->execute($auditLogId, $relationships);

        if (is_null($auditLog) || $loggedInUser->company_id !== $auditLog->company_id) {
            return generateErrorApiMessage('Audit log record does not exists', 404);
        }

        $mutatedAuditLog = new GetAuditLogResource($auditLog);

        return generateSuccessApiMessage('Audit log was retrieved successfully', 200, $mutatedAuditLog);
    }
}
