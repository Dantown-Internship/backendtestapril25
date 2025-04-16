<?php

namespace App\Http\Requests\Expense;

use App\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|string'
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.string' => 'The title should be character',
            'amount.min' => 'Amount cannot be less than 1',
            'category.string' => 'Category should be character',

        ];
    }
}
