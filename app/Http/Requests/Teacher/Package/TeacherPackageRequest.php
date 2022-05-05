<?php

namespace App\Http\Requests\Teacher\Package;

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
                'required_unless:package,per_day',
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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'price_per_hour.required' => 'Harga tidak boleh kosong',
            'price_per_hour.min' => 'Harga minimal 50.000',
            'price_per_hour.integer' => 'Harga haruslah angka',
            'encounter.required' => 'Jumlah pertemuan tidak boleh kosong',
            'encounter.integer' => 'Jumlah pertemuan haruslah angka',
        ];
    }
}
