<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Admin\LessonSubject\LessonSubjectUpdateThumbnailRequest;
use App\Http\Requests\Admin\LessonSubject\LessonSubjectRequest;
use Kreait\Firebase\Contract\Storage;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Firebase\FirebaseStorage;
use App\Models\LessonSubject;

class LessonSubjectController extends Controller
{

    private $firebaseStorage;

    public function __construct(Storage $storage)
    {
        $this->firebaseStorage = new FirebaseStorage($storage);
    }

    public function getAllLessonSubject()
    {
        $lessonSubjects = LessonSubject::select('id', 'name', 'description', 'thumbnail')
            ->orderBy('created_at', 'desc')
            ->when(request('limit'), function($query) {
                return $query->limit(request('limit'));
            })
            ->get();

        return response()->json([
            'message' => 'Sukses mengambil mata pelajaran guru',
            'data' => $lessonSubjects
        ], 200);
    }

    public function getOneLessonSubject($lessonSubjectId)
    {
        $lessonSubject = LessonSubject::select(['id', 'name', 'description', 'thumbnail'])
            ->find($lessonSubjectId);
        if (is_null($lessonSubject)) throw new NotFoundException('Mata pelajaran tidak ditemukan');

        return response()->json([
            'message' => 'Sukses mengambil mata pelajaran guru',
            'data' => $lessonSubject
        ], 200);
    }

    public function createLessonSubject(LessonSubjectRequest $lessonSubjectRequest)
    {
        $lessonSujectData = $lessonSubjectRequest->validated();
        $lessonSubjectThumbnail = $lessonSubjectRequest->file('thumbnail');

        $thumbnailName = $this->firebaseStorage->uploadFile($lessonSubjectThumbnail, 'lesson_subjects/');

        $lessonSujectData['thumbnail'] = $thumbnailName;
        LessonSubject::create($lessonSujectData);
        return response()->json([
            'status' => 201,
            'message' => 'Berhasil menambah mata pelajaran guru'
        ], 201);
    }

    public function updateLessonSubject(LessonSubjectRequest $lessonSubjectRequest, $lessonSubjectId)
    {
        $lessonSubject = LessonSubject::select(['id', 'name', 'description', 'thumbnail'])->find($lessonSubjectId);
        if (is_null($lessonSubject)) throw new NotFoundException('Mata pelajaran tidak ditemukan');

        $updatedLessonSubjectData = $lessonSubjectRequest->validated();
        $lessonSubject->name = $updatedLessonSubjectData['name'];
        $lessonSubject->description = $updatedLessonSubjectData['description'];
        $lessonSubject->save();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengubah mata pelajaran guru'
        ], 200);
    }

    public function updateLessonSubjectThumbnail(LessonSubjectUpdateThumbnailRequest $lessonSubjectUpdateThumbnailRequest, $lessonSubjectId)
    {
        $lessonSubject = LessonSubject::select(['id', 'thumbnail'])->find($lessonSubjectId);
        if (is_null($lessonSubject)) throw new NotFoundException('Mata pelajaran tidak ditemukan');

        $updatedLessonSubjectThumbnail = $lessonSubjectUpdateThumbnailRequest->file('thumbnail');

        $thumbnailName = $this->firebaseStorage->updateFile($updatedLessonSubjectThumbnail, 'lesson_subjects/', $lessonSubject->thumbnail);

        $lessonSubject->thumbnail = $thumbnailName;
        $lessonSubject->save();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengubah thumbnail mata pelajaran guru'
        ], 200);
    }

    public function deleteLessonSubject($lessonSubjectId)
    {
        $lessonSubject = LessonSubject::select(['id', 'thumbnail'])
            ->find($lessonSubjectId);
        if (is_null($lessonSubject)) throw new NotFoundException('Mata pelajaran tidak ditemukan');
        $lessonSubject->delete();
        $this->firebaseStorage->deleteFile($lessonSubject->thumbnail, 'lesson_subjects/');
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menghapus mata pelajaran guru'
        ], 200);
    }
}
