<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !auth("sanctum")->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "full_name" => ["required", "string", "max:80"],
            "email" => ["required", "email", "unique:users,email"],
            "phone_number" => ["required", "digits_between:10,15", "unique:users,phone_number"],
            "password" => ["required", "string", "min:8"],
            "verify_password" => ["required", "string", "same:password"],
            "role" => [
                "required",
                Rule::in(["teacher", "student"])
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
            'full_name.max' => 'Nama tidak boleh lebih panjang dari 80 karakter',
            'full_name.string' => 'Nama haruslah sebuah alphabet',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak benar',
            'email.unique' => 'Email sudah digunakan',
            'phone_number.required' => 'No telp tidak boleh kosong',
            'phone_number.digits_between' => 'No telp harus antar 10-15 digit',
            'phone_number.unique' => 'No telp sudah digunakan',
            'password.required' => 'Password tidak boleh kosong',
            'password.string' => 'Password harus alphabet',
            'password.min' => 'Password minimal 8 karakter',
            'verify_password.required' => 'Verifikasi password tidak boleh kosong',
            'verify_password.same' => 'Verifikasi password tidak sama dengan password',
            'role.required' => 'Harap pilih role',
            'role.in' => 'Role haruslah \'Student\' atau \'Teacher\'',
        ];
    }
}
