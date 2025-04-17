<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'bail|required|string',
            'email' => 'bail|required|unique:users,email|email',
            'password' => 'bail|required|max:12',
            'company_id' => 'bail|required|exists:companies,id|unique:users,company_id',
            'role' => 'bail|required|string'
        ];
    }
}
