<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add role check if needed
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
        ];
    }
}
