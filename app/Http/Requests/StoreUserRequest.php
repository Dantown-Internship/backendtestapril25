<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{

    public function authorize(): bool
    {
        // Only admins can create users
        return $this->user() && $this->user()->isAdmin();
    }

    
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:Admin,Manager,Employee',
            'company_id' => 'sometimes|exists:companies,id',
        ];
    }

    
    protected function prepareForValidation(): void
    {
        // Always use the authenticated user's company_id
        if ($this->user()) {
            $this->merge([
                'company_id' => $this->user()->company_id,
            ]);
        }
    }
}
