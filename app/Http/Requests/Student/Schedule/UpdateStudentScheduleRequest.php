<?php

namespace App\Http\Requests\Student\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentScheduleRequest extends FormRequest
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
            'date' => ['required', 'string', 'date_format:Y-m-d'],
            'from_time' => ['required', 'string', 'date_format:H:i'],
            'to_time' => ['required', 'string', 'date_format:H:i', 'after:from_time'],
            'note' => ['required', 'string'],
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
            'date.required' => 'Tanggal tidak boleh kosong',
            'date.date_format' => 'Format tanggal salah',
            'from_time.required' => 'Jam mulai belajar tidak boleh kosong',
            'from_time.date_format' => 'Format jam mulai belajar salah',
            'to_time.required' => 'Jam akhir belajar tidak boleh kosong',
            'to_time.date_format' => 'Format jam akhir belajar salah',
            'to_time.after' => 'Jam akhir belajar harus sesudah jam mulai belajar',
            'note.required' => 'Note tidak boleh kosong',
        ];
    }
}
