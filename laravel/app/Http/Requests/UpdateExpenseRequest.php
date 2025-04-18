<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title'     => 'sometimes|required|string|max:255',
            'amount'    => 'sometimes|required|numeric',
            'category'  => 'sometimes|required|string|max:255',
        ];
    }
}
