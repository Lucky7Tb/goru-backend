<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class updateTransactionStudent extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('sanctum')->check() && request()->user()->role === 'Student';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'application_bank_account' => [
                'required',
                'uuid'
            ],
            'evidance' => [
                'required',
                'string'
            ],
        ];
    }
}
