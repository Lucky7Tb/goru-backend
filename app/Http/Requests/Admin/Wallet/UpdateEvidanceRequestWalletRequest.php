<?php

namespace App\Http\Requests\Admin\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEvidanceRequestWalletRequest extends FormRequest
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
            'evidance' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2024']
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
            'evidance.required' => 'Bukti transfer tidak boleh kosong',
            'evidance.image' => 'Bukti transfer harus berupa gambar',
            'evidance.mimes' => 'Format gambar yang didukung berupa (PNG,JPG,JPEG)',
            'evidance.max' => 'Gambar bukti transfer terlalu besar, Max 2MB',
        ];
    }
}
