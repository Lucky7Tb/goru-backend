<?php

namespace App\Http\Requests\Teacher\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleMeetEvidanceRequest extends FormRequest
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
            'meet_evidance' => [ 'required', 'image', 'mimes:png,jpg,jpeg', 'max:2024']
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
            'meet_evidance.required' => 'Bukti tidak boleh kosong',
            'meet_evidance.image' => 'Bukti haruslah gambar',
            'meet_evidance.mimes' => 'Format bukti harus(PNG|JPG|JPEG)',
            'meet_evidance.max' => 'Maksimal besaran bukti adalah 2MB',
        ];
    }
}
