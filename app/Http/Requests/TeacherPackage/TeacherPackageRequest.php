<?php

namespace App\Http\Requests\TeacherPackage;

use Illuminate\Foundation\Http\FormRequest;

class TeacherPackageRequest extends FormRequest
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
        $validation = [
            'price_per_hour' => [
                'required',
                'min:50000',
                'integer'
            ],
            'encounter' => [
                'required_unless:packager,per_day',
                'integer'
            ]
        ];

        if (request()->getMethod() == 'POST') {
            $validation['package'] = [
                'required',
                'in:per_day,per_week,per_month'
            ];
        }

        return $validation;
    }
}
