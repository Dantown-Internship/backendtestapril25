<?php

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('audit_log')) {
    function audit_log($action, $userId, $companyId, $resource, $old, $new)
    {
        AuditLog::create([
            'user_id' => $userId,
            'company_id' => $companyId,
            'action' => "{$action} {$resource}",
            'changes' => json_encode([
                'old' => $old,
                'new' => $new,
            ]),
        ]);
    }
}

if (!function_exists('companyID')) {
    function companyID()
    {
        return Auth::check() ? Auth::user()->company_id : null;
    }
}

if (!function_exists('userID')) {
    function userID()
    {
        return Auth::check() ? Auth::user()->id : null;
    }
}