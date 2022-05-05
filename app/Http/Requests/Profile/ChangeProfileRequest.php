<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth("sanctum")->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'full_name' => ['required', 'string'],
            'phone_number' => [
                "required",
                "digits_between:10,15",
                Rule::unique('users', 'phone_number')->ignore($this->user()->id)
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
            'full_name.required' => 'Nama tidak boleh kosong',
            'phone_number.required' => 'No telepon tidak boleh kosong',
            'phone_number.digits_between' => 'No telpon minimal 10-15 digit',
            'phone_number.unique' => 'No telpon sudah ada yang menggunakan'
        ];
    }
}
