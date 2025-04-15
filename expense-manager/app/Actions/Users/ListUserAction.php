<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListUserAction
{
    public function handle($search, $perPage)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $query = User::where('company_id', $companyId);

        // Apply search filters
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('role', 'like', '%' . $search . '%');
            });
        }
        return $query->latest()->paginate($perPage);
    }
}
