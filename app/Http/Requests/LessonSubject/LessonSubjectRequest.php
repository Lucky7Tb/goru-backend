<?php

namespace App\Http\Requests\LessonSubject;

use Illuminate\Foundation\Http\FormRequest;

class LessonSubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth("sanctum")->check() && request()->user()->role === 'Admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validation = [
            'name' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:100'],
        ];

        if (request()->getMethod() == 'POST') {
            $validation['thumbnail'] = ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2024'];
        }

        return $validation;
    }
}
