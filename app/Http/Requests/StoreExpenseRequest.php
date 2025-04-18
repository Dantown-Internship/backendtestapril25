<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->user()->id,
            'company_id' => $this->user()->company_id,
        ]);
    }
}
