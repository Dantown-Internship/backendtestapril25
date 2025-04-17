<?php

namespace App\Http\Requests;

use App\Enums\AuditLogAction;
use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListAuditLogsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === Role::Admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer'],
            'action' => ['nullable', Rule::enum(AuditLogAction::class)],
            'from_date' => [
                'sometimes',
                'nullable',
                'date_format:Y-m-d',
                'before_or_equal:to_date',
                'before_or_equal:today'
            ],
            'to_date' => [
                'sometimes',
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:from_date',
                'before_or_equal:today'
            ],
        ];
    }
}
