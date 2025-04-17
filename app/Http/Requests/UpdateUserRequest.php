<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Http\Controllers\Concerns\HasApiResponse;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    use HasApiResponse;

    public ?User $userToBeUpdated = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->userToBeUpdated->company_id === $this->user()->company_id
            && $this->user()->role === Role::Admin;
    }

    public function prepareForValidation(): void
    {
        $this->userToBeUpdated = User::where('uuid', $this->route('user'))->firstOrFail();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($this->userToBeUpdated->id)],
            'role' => ['required', 'string', Rule::enum(Role::class)],
        ];
    }
}
