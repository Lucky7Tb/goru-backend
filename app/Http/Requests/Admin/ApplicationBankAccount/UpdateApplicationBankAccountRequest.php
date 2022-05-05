<?php

namespace App\Http\Requests\Admin\ApplicationBankAccount;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationBankAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth("sanctum")->check() && request()->user()->role === 'Admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
            'number' => ['required', 'numeric'],
            'alias' => ['required', 'string'],
            'is_active' => ['required', 'boolean'],
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
            'name.required' => 'Nama jenjang pendidikan tidak boleh kosong',
            'number.required' => 'No rekening tidak boleh kosong',
            'number.numeric' => 'No rekening haruslah angka',
            'alias.required' => 'Thumbnail tidak boleh kosong',
            'is_active.required' => 'Status aktif tidak boleh kosong',
            'is_active.boolean' => 'Status aktif antara true atau false',
        ];
    }
}
