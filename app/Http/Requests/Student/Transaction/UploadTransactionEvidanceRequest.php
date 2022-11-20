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
            'evidance' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2024'],
            'sender_account_number' => ['required', 'numeric'],
            'sender_account_name' => ['required', 'string', 'max:100'],
            'sender_bank_name' => ['required', 'string', 'max:80']
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
            'sender_account_number.required' => 'No rekening tidak boleh kosong',
            'sender_account_number.numeric' => 'No rekening hanya berupa angka',
            'sender_account_name.required' => 'Nama pengirim rekening tidak boleh kosong',
            'sender_account_name.max' => 'Nama pengirim maksimal panjangnya 100 karakter',
            'sender_bank_name.required' => 'Nama bank tidak boleh kosong',
            'sender_bank_name.max' => 'Nama bank maksimal panjangnya 80 karakter',
        ];
    }
}
