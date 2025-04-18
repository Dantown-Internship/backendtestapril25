<?php

namespace App\Http\Requests;

use App\Constants\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRoleRequest extends FormRequest
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
            'role' => ['required', 'string', Rule::in([
                UserRole::ADMIN,
                UserRole::MANAGER,
                UserRole::EMPLOYEE,
            ])],
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'The selected role must be one of: admin, manager, or employee.',
        ];
    }
}
