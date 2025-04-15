<?php

namespace App\Actions\Expenses;

use App\Models\Expense;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreExpensesAction
{
    public function handle(array $data)
    {
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $data['company_id'] = $user->company_id;

        $expense = Expense::Create($data);

        return $expense;

    }
}
