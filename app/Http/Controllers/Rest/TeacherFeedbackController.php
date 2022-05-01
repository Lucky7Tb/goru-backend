<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Student\Feedback\GiveTeacherFeedbackRequest;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Models\TeacherComment;
use App\Models\TeacherRating;
use App\Models\Schedule;
use App\Models\User;

class TeacherFeedbackController extends Controller
{
    public function getTeacherFeedback()
    {

    }

    public function giveTeacherFeedback(string $teacherId, GiveTeacherFeedbackRequest $teacherFeedbacks)
    {
        $teacherFeedbackData = $teacherFeedbacks->validated();

        $teacher = User::select('id')->find($teacherId);
        if (is_null($teacher)) throw new NotFoundException('Guru tidak ditemukan');

        TeacherRating::create([
            'student_id' => auth()->user()->id,
            'teacher_id' => $teacher->id,
            'rating' => $teacherFeedbackData['rating']
        ]);

        TeacherComment::create([
            'student_id' => auth()->user()->id,
            'teacher_id' => $teacher->id,
            'comment' => $teacherFeedbackData['comment']
        ]);

        Schedule::find($teacherFeedbackData['schedule_id'])
            ->update([
                'is_already_feedback' => 1
            ]);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil memberi feedback kepada guru',
        ]);
    }
}
