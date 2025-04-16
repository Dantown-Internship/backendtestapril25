<?php

namespace App\Actions\Expenses;

use App\Models\Expense;
use App\Services\CacheService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StoreExpensesAction
{
    public function handle(array $data)
    {
        $user = Auth::user();
        Gate::authorize('create', Expense::class);

        $data['user_id'] = $user->id;
        $data['company_id'] = $user->company_id;

        $expense = Expense::Create($data);

        // Clear expenses cache for this company
        CacheService::clearCompanyCache('expenses');

        return $expense;
    }
}
