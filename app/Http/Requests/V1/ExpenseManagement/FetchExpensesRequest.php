<?php

namespace App\Http\Requests\V1\ExpenseManagement;

use Illuminate\Foundation\Http\FormRequest;

class FetchExpensesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search_query' => ['nullable', 'string'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ];
    }

    public function messages(): array
    {
        return [
            'page.integer' => 'The page must be an integer',
            'page.min' => 'The page must be at least 1',

            'per_page.integer' => 'The per page must be an integer',
            'per_page.between' => 'The per page must be between 1 to 100',
        ];
    }
}
