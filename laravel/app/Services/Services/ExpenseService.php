<?php

namespace App\Services\Services;

use App\Models\Expense;
use App\Services\ServiceParent;
use Illuminate\Support\Facades\Cache;

class ExpenseService extends ServiceParent
{
    public function __construct()
    {
        parent::__construct(Expense::class);
    }

    public function getForCompany($companyId)
    {
        return Cache::remember("expenses_{$companyId}", 60, function () use ($companyId) {
            return $this->model::where('company_id', $companyId)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function createExpense(array $data)
    {
        return $this->create([
            'title'     => $data['title'],
            'amount'    => $data['amount'],
            'category'  => $data['category'],
            'company_id'=> $data['company_id'],
            'user_id'   => $data['user_id'],
        ]);
    }

    public function updateExpense($id, array $data)
    {
        return $this->update($id, [
            'title'     => $data['title'] ?? $this->model::find($id)->title,
            'amount'    => $data['amount'] ?? $this->model::find($id)->amount,
            'category'  => $data['category'] ?? $this->model::find($id)->category,
        ]);
    }
}
