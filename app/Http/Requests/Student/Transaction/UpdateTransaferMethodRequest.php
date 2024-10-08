<?php

namespace App\Http\Requests\Student\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransaferMethodRequest extends FormRequest
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
            'application_bank_account_id' => [
                'required',
                'uuid'
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'application_bank_account_id.required' => 'Bank harus dipilih',
            'application_bank_account_id.uuid' => 'Format id bank salah',
        ];
    }
}
