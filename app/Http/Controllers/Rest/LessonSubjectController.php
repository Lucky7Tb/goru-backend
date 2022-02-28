<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\LessonSubject\LessonSubjectRequest;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\LessonSubject;

class LessonSubjectController extends Controller
{
    public function getAllLessonSubject() : JsonResponse
    {
        $lessonSubjects = LessonSubject::select(['id', 'name', 'description'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Sukses mengambil mata pelajaran guru',
            'data' => $lessonSubjects
        ], 200);
    }

    public function getOneLessonSubject($lessonSubjectId)
    {
        $lessonSuject = LessonSubject::select(['id', 'name', 'description'])
            ->where('id', $lessonSubjectId)
            ->first();
        if ($lessonSuject === null) throw new NotFoundException('Mata pelajaran tidak ditemukan');

        return response()->json([
            'message' => 'Sukses mengambil mata pelajaran guru',
            'data' => $lessonSuject
        ], 200);
    }

    public function createLessonSubject(LessonSubjectRequest $lessonSubjectRequest)
    {
        $lessonSujectData = $lessonSubjectRequest->validated();
        LessonSubject::create($lessonSujectData);
        return response()->json([
            'status' => 201,
            'message' => 'Berhasil menambah mata pelajaran guru'
        ], 201);
    }

    public function updateLessonSubject(LessonSubjectRequest $lessonSubjectRequest, $lessonSubjectId)
    {
        $lessonSubject = LessonSubject::select(['id', 'name', 'description'])->where('id', $lessonSubjectId)->first();
        if ($lessonSubject === null) throw new NotFoundException('Mata pelajaran tidak ditemukan');

        $updatedLessonSubjectData = $lessonSubjectRequest->validated();
        $lessonSubject->name = $updatedLessonSubjectData['name'];
        $lessonSubject->description = $updatedLessonSubjectData['description'];
        $lessonSubject->save();
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengubah mata pelajaran guru'
        ], 200);
    }

    public function deleteLessonSubject($lessonSubjectId)
    {
        $lessonSubject = LessonSubject::select(['id', 'name', 'description'])
            ->where('id', $lessonSubjectId)
            ->first();
        if ($lessonSubject === null) throw new NotFoundException('Mata pelajaran tidak ditemukan');
        $lessonSubject->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menghapus mata pelajaran guru'
        ], 200);
    }
}
