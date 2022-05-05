<?php

namespace App\Http\Requests\Teacher\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('sanctum')->check() && request()->user()->role === 'Teacher';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'note' => [
                'required',
                'string'
            ],
            'status' => [
                'required',
                'in:rejected,accepted'
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
            'note.required' => 'Note tidak boleh kosong',
            'status.required' => 'status tidak boleh kosong',
            'status.in' => 'Status jadwal haruslah rejected atau accepted',
        ];
    }
}
