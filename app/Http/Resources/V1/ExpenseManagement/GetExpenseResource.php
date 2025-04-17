<?php

namespace App\Http\Resources\V1\ExpenseManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'expense_category' => [
                'id' => $this->expenseCategory->id,
                'name' => $this->expenseCategory->name,
            ],
            'title' => $this->title,
            'amount' => $this->amount,
        ];
    }
}
