<?php

namespace App\Http\Requests\Admin\LessonSubject;

use Illuminate\Foundation\Http\FormRequest;

class LessonSubjectUpdateThumbnailRequest extends FormRequest
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
            'thumbnail' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2024']
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
            'thumbnail.required' => 'Thumbnail tidak boleh kosong',
            'thumbnail.image' => 'Thumbnail harus berupa gambar',
            'thumbnail.mimes' => 'Format gambar yang didukung berupa (PNG,JPG,JPEG)',
            'thumbnail.max' => 'Gambar bukti transfer terlalu besar, Max 2MB',
        ];
    }
}
