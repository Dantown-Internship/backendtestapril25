<?php

namespace App\Http\Requests;

use App\Enums\ExpenseCategory;
use App\Enums\Role;
use App\Models\Expense;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseRequest extends FormRequest
{
    public Expense $expense;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()->role, [Role::Admin, Role::Manager]);
    }

    protected function prepareForValidation(): void
    {
        $this->expense = Expense::where('uuid', $this->route('uuid'))->firstOrFail();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category' => ['required', Rule::enum(ExpenseCategory::class)],
        ];
    }
}
