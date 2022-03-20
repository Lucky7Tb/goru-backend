<?php

namespace App\Http\Requests\Admin\ApplicationBankAccount;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationBankAccountLogoRequest extends FormRequest
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
            'bank_logo' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2024']
        ];
    }
}
