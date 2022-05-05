<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Student\Feedback\GiveTeacherFeedbackRequest;
use App\Exceptions\NotFoundException;
use App\Exceptions\BadRequestException;
use App\Http\Controllers\Controller;
use App\Models\TeacherComment;
use App\Models\TeacherRating;
use App\Models\Schedule;
use App\Models\User;

class TeacherFeedbackController extends Controller
{
    public function getTeacherFeedback()
    {
        $feedbackType = request('type');
        $feedback = [];

        switch($feedbackType) {
            case 'all':
                $comments = TeacherComment::select('id', 'student_id', 'comment')
                    ->with([
                        'student:id,full_name,photo_profile'
                    ])
                    ->where('teacher_id', '=', auth()->user()->id)
                    ->when(request('limit'), function ($query) {
                        return $query->limit(request('limit'));
                    })
                    ->get();
                $ratings = TeacherRating::select('id', 'student_id', 'rating')
                    ->with([
                        'student:id,full_name,photo_profile'
                    ])
                    ->where('teacher_id', '=', auth()->user()->id)
                    ->when(request('limit'), function ($query) {
                        return $query->limit(request('limit'));
                    })
                    ->get();
                $feedback['comments'] = $comments;
                $feedback['ratings'] = $ratings;
                break;
            case 'comment':
                $comments = TeacherComment::select('id', 'student_id', 'comment')
                    ->with([
                        'student:id,full_name,photo_profile'
                    ])
                    ->where('teacher_id', '=', auth()->user()->id)
                    ->orderBy('created_at', 'desc')
                    ->when(request('limit'), function ($query) {
                        return $query->limit(request('limit'));
                    })
                    ->get();
                $feedback['comments'] = $comments;
                break;
            case 'rating':
                $ratings = TeacherRating::select('id', 'student_id', 'rating')
                    ->with([
                        'student:id,full_name,photo_profile'
                    ])
                    ->where('teacher_id', '=', auth()->user()->id)
                    ->when(request('limit'), function ($query) {
                        return $query->limit(request('limit'));
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
                $feedback['ratings'] = $ratings;
                break;
            default:
                throw new BadRequestException('Tipe feedback tidak diketahui');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil feedback',
            'data' => $feedback
        ], 200);
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
