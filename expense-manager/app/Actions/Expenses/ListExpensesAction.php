<?php

namespace App\Actions\Expenses;

use App\Enums\Roles;
use App\Models\Expense;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListExpensesAction
{
    public function handle(array $filters, int $perPage): LengthAwarePaginator
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $query = Expense::with('user')->where('company_id', $companyId);

        // Show only empoyee expenses
        if ($user->role === Roles::EMPLOYEE) {
            $query->where('user_id', $user->id);
        }

        // Apply search filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('category', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply category filter
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        return $query->latest()->paginate($perPage);
    }
}
