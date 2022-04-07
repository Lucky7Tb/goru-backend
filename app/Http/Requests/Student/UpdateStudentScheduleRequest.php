<?php

namespace App\Http\Requests\Student;

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
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'from_time' => ['required', 'string', 'date_format:H:i'],
            'to_time' => ['required', 'string', 'date_format:H:i'],
            'note' => ['string'],
        ];
    }
}
