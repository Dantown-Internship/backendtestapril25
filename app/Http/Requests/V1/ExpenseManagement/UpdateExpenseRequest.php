<?php

namespace App\Http\Requests\V1\ExpenseManagement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'expense_category_id' => ['required', 'uuid', 'exists:expense_categories,id'],
            'title' => ['required', 'string', 'between:1,200'],
            'amount' => ['required', 'integer', "min:0"],
        ];
    }

    public function messages(): array
    {
        return [
            'expense_category_id.required' => 'Please select an expense category.',
            'expense_category_id.uuid' => 'The expense category ID must be a valid UUID.',
            'expense_category_id.exists' => 'The selected expense category does not exist.',

            'title.required' => 'The title is required.',
            'title.string' => 'The title must be a valid string.',
            'title.between' => 'The title must be between 1 and 200 characters.',

            'amount.required' => 'The amount is required.',
            'amount.integer' => 'The amount must be an integer number.',
        ];
    }
}
