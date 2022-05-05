<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class ChangePhotoProfileRequest extends FormRequest
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
            'photo_profile' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2024']
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
            'photo_profile.required' => 'Foto tidak boleh kosong',
            'photo_profile.image' => 'Foto haruslah format gambar',
            'photo_profile.mimes' => 'Format foto harus (PNG|JPG|JPEG)',
            'photo_profile.max' => 'Ukuran foto maksimal 2MB'
        ];
    }
}
