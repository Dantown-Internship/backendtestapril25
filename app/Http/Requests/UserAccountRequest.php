<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserAccountRequest extends FormRequest
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
    public function rules($user): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'name' => 'required|string|min:3|max:255|unique:companies,email',
            'email' => 'required|email|max:255|unique:companies,email',
            'email' => [
                'require','email',
                Rule::unique('users')->where(function ($query) use ($user) {
                    return $query->where('company_id', $user->company_id);
                }),
            ],
            'phone' =>[
                'required|string|max:12',
                Rule::unique('users')->where(function ($query) use ($user) {
                    return $query->where('company_id', $user->company_id);
                }),
            ],
            'password' => 'required|min:6',
        ];
    }
}
