<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Teacher\LessonSubject\TeacherLessonSubjectRequest;
use App\Exceptions\AlreadyTakenException;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Models\TeacherLessonSubject;
use App\Models\LessonSubject;

class TeacherLessonSubjectController extends Controller
{
    public function getAllTeacherLessonSubject()
    {
        $lessonSubjects = TeacherLessonSubject::with(['lessonSubject:id,name'])
            ->where('user_id', '=', auth()->user()->id)
            ->get(['id', 'lesson_subject_id']);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil mata pelajaran ajar guru',
            'data' => $lessonSubjects
        ], 200);
    }

    public function createTeacherLessonSubject(TeacherLessonSubjectRequest $teacherLessonSubjectRequest)
    {
        $lessonSubjectId = $teacherLessonSubjectRequest->validated('lesson_subject_id');
        $level = LessonSubject::select('id')->find($lessonSubjectId);
        if (is_null($level)) throw new NotFoundException('Data mata pelajaran tidak ditemukan');

        $teacherLessonSubject = TeacherLessonSubject::where([
            'lesson_subject_id' => $lessonSubjectId,
            'user_id' => auth()->user()->id
        ])->get(['id'])->count();

        if ($teacherLessonSubject == 1) {
            throw new AlreadyTakenException('Kamu udah ngambil mata pelajaran ini nih');
        }

        TeacherLessonSubject::create([
            'lesson_subject_id' => $lessonSubjectId,
            'user_id' => auth()->user()->id
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Kamu berhasil mengambil mata pelajaran ini'
        ], 201);
    }

    public function deleteTeacherLessonSubject(string $teacherLessonSubjectId)
    {
        $teacherLessonSubject = TeacherLessonSubject::select('id')->find($teacherLessonSubjectId);
        if (is_null($teacherLessonSubject)) {
            throw new NotFoundException('Data mata pelajaran kamu tidak ditemukan');
        }

        $teacherLessonSubject->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menghapus mata pelajaran ajar kamu'
        ], 200);
    }
}
