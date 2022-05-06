<?php

namespace App\Http\Requests\Teacher\Level;

use Illuminate\Foundation\Http\FormRequest;

class TeacherLevelRequest extends FormRequest
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
            'level_id' => ['required', 'uuid']
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
            'level_id.required' => 'Pilihan jenjang pendidikan tidak boleh kosong',
        ];
    }
}
