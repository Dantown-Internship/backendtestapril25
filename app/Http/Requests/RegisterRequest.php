<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,employee,Admin,Manager,Employee',
        ];
    }
    
    protected function prepareForValidation()
    {
        if ($this->has('role')) {
            $this->merge([
                'role' => strtolower($this->role),
            ]);
        }
    }
}
