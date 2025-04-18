<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only Admin and Manager can update Expense
        return auth()->check() && in_array(
            auth()->user()->role->value,
            ['Admin', 'Manager'],
            true
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'    => 'sometimes|required|string|max:255',
            'amount'   => 'sometimes|required|numeric|min:0',
            'category' => 'sometimes|required|string|max:100',
        ];
    }
}
