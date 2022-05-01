<?php

namespace App\Http\Requests\Student\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class GiveTeacherFeedbackRequest extends FormRequest
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
            'schedule_id' => ['required', 'integer'],
            'comment' => ['required', 'string', 'max:100'],
            'rating' => ['required', 'min:1', 'integer', 'between:1,5'],
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
            'schedule_id.required' => 'Id jadwal tidak boleh kosong',
            'schedule_id.integer' => 'Id jadwal haruslah angka',
            'comment.required' => 'Komen tidak boleh kosong',
            'comment.string' => 'Komen tidak boleh berupa angka',
            'comment.max' => 'Komen tidak boleh lebih dari 100 karakter',
            'rating.required' => 'Rating tidak boleh kosong',
            'rating.min' => 'Minimal rating adalah 1',
            'rating.integer' => 'Rating haruslah berupa angka',
            'rating.between' => 'Rating harus dalam skala 1-5'
        ];
    }
}
