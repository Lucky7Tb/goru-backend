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
            'meet_evidance' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg',
                'max:2024'
            ]
        ];
    }
}
