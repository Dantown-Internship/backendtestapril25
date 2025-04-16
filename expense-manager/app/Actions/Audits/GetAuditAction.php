<?php

namespace App\Actions\Audits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class GetAuditAction
{
    public function handle($id)
    {
        $user = Auth::user();

        $audit = AuditLog::where('company_id', $user->company_id)->findOrFail($id);

        return $audit->load(['user', 'expense']);
    }
}
