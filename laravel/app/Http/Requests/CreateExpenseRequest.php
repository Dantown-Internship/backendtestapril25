<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title'     => 'required|string|max:255',
            'amount'    => 'required|numeric',
            'category'  => 'required|string|max:255',
            'company_id'=> 'required|exists:companies,id',
            'user_id'   => 'required|exists:users,id',
        ];
    }
}
