<?php

namespace App\Http\Requests\Admin\LessonSubject;

use Illuminate\Foundation\Http\FormRequest;

class LessonSubjectRequest extends FormRequest
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
        $validation = [
            'name' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:100'],
        ];

        if (request()->getMethod() == 'POST') {
            $validation['thumbnail'] = ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2024'];
        }

        return $validation;
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
            'name.max' => 'Nama jenjang pendidikan maksimal 50 karakter',
            'description.required' => 'Deskripsi tidak boleh kosong',
            'description.max' => 'Deskripsi maksimal 100 karakter',
            'thumbnail.required' => 'Thumbnail tidak boleh kosong',
            'thumbnail.image' => 'Thumbnail harus berupa gambar',
            'thumbnail.mimes' => 'Format gambar yang didukung berupa (PNG,JPG,JPEG)',
            'thumbnail.max' => 'Gambar bukti transfer terlalu besar, Max 2MB',
        ];
    }
}
