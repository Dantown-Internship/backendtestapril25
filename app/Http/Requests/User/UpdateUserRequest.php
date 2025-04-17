<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'       => 'sometimes|string|max:255',
            'password'   => 'sometimes|string|min:8|confirmed',
            'company_id' => 'sometimes|string|exists:companies,id',
            'role_id'    => 'sometimes|integer|exists:roles,id',
            'status'     => 'sometimes|string|in:active,locked',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'Validation errors',
            'errors'  => $validator->errors()
        ], 422));
    }
}
