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
}
