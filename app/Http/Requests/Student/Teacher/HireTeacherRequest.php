<?php

namespace App\Http\Requests\Student\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class HireTeacherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth("sanctum")->check() && request()->user()->role === 'Student';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'package_id' => ['required', 'uuid'],
            'note' => ['required', 'max:100'],
            'schedules' => ['required', 'array'],
            'schedules.*.date' => ['required', 'string', 'date_format:Y-m-d'],
            'schedules.*.from_time' => ['required', 'string', 'date_format:H:i'],
            'schedules.*.to_time' => ['required', 'string', 'date_format:H:i', 'after:schedules.*.from_time'],
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
            'package_id.required' => 'Harus memilih pake guru',
            'note.required' => 'Note tidak boleh kosong',
            'note.max' => 'Note maksimal 100 karakter',
            'schedules.required' => 'Jadwal harus diisi',
            'schedules.*.date.required' => 'Tanggal tidak boleh kosong',
            'schedules.*.date.date_format' => 'Format tanggal salah',
            'schedules.*.from_time.required' => 'Jam mulai belajar harus diisi',
            'schedules.*.from_time.date_format' => 'Format jam mulai belajar salah',
            'schedules.*.to_time.required' => 'Jam akhir belajar harus diisi',
            'schedules.*.to_time.date_format' => 'Format jam akhir belajar salah',
            'schedules.*.to_time.after' => 'Jam akhir belajar harus sesudah jam mulai belajar',
        ];
    }
}
