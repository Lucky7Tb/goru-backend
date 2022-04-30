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
            'note' => ['max:100'],
            'schedules' => ['required', 'array'],
            'schedules.*.date' => ['required', 'date', 'date_format:Y-m-d'],
            'schedules.*.from_time' => ['required', 'string', 'date_format:H:i'],
            'schedules.*.to_time' => ['required', 'string', 'date_format:H:i'],
        ];
    }
}
