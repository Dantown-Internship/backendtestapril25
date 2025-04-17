<?php

namespace App\Http\Requests\V1\Settings\ExpenseCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'between:1,200', Rule::unique('expense_categories', 'name')->ignore($this->expenseCategoryId),],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The expense category name is required.',
            'name.string' => 'The expense category name must be a string.',
            'name.between' => 'The expense category name must be between 1 and 200 characters.',
            'name.unique' => 'The expense category name has already been taken.',
        ];
    }
}
