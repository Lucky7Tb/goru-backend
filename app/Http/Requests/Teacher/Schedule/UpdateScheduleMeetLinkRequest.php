<?php

namespace App\Http\Requests\Teacher\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleMeetLinkRequest extends FormRequest
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
            'meet_link' => ['required', 'url']
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
            'meet_link.required' => 'Link pembelajaran tidak boleh kosong',
            'meet_link.url' => 'Format link tidak benar',
        ];
    }
}
