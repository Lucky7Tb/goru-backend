<?php

namespace App\Http\Requests\Student\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class UploadTransactionEvidanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('sanctum')->check() && request()->user()->role === 'Student';
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
            'evidance.required' => 'Bukti tidak boleh kosong',
            'evidance.image' => 'Bukti haruslah gambar',
            'evidance.mimes' => 'Format bukti harus(PNG|JPG|JPEG)',
            'evidance.max' => 'Maksimal besaran bukti adalah 2MB',
        ];
    }
}
