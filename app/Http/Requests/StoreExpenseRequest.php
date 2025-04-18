<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam title string required The title or description of the expense. Max 255 characters. Example: Office Supplies
 * @bodyParam category string required The category of the expense. Max 100 characters. Example: Equipment
 * @bodyParam amount number required The amount spent. Must be zero or greater. Example: 149.99
 */
class StoreExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'    => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'amount'   => ['required', 'numeric', 'min:0'],
        ];
    }
}
