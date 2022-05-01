<?php

namespace App\Http\Requests\Teacher\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequestWalletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth("sanctum")->check() && request()->user()->role === 'Teacher';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bank_name' => ['required', 'string'],
            'bank_account_number' => ['required', 'string'],
            'request_ammount' => ['required', 'integer', 'min:50000'],
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
            'bank_name.required' => 'Nama bank tidak boleh kosong',
            'bank_account_number.required' => 'No rekening tidak boleh kosong',
            'request_ammount.required' => 'Besaran saldo yang akan dicairkan tidak boleh kosong',
            'request_ammount.integer' => 'Besaran saldo yang akan dicairkan haruslah angka',
            'request_ammount.min' => 'Besaran saldo yang akan dicairkan minimal 50.000',
        ];
    }
}
