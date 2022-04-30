<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\GiveTeacherFeedbackRequest;
use App\Models\TeacherComment;
use App\Models\TeacherRating as ModelsTeacherRating;
use App\Models\User;
use App\Exceptions\NotFoundException;
use Illuminate\Http\Request;

class TeacherFeedback extends Controller
{
    public function giveTeacherFeedback(GiveTeacherFeedbackRequest $teacherFeedbacks , string $teacherId){
        $teacherFeedbackData = $teacherFeedbacks->validate();

        $teacher = User::select('id')->find($teacherId);
        if (is_null($teacher)) throw new NotFoundException('Guru tidak ditemukan');


        ModelsTeacherRating::create([
            'student_id' => auth()->user()->id,
            'teacher_id' => $teacher->id,
            'rating' => $teacherFeedbackData['rating']
        ]);

        TeacherComment::create([
            'student_id' => auth()->user()->id,
            'teacher_id' => $teacher->id,
            'comment' => $teacherFeedbackData['comment']
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil memberi feedback',
        ]);
    }
    


    
}
