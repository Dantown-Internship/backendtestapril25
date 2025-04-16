<?php

namespace App\Actions\Audits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListAuditAction
{
    public function handle($filters, $perPage)
    {
        $user = Auth::user();
        $query = AuditLog::with(['user', 'expense'])->where('company_id', $user->company_id);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['expense_id'])) {
            $query->where('expense_id', $filters['expense_id']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', $filters['type']);
        }
            return $query->latest()->paginate($perPage);
    }
}
