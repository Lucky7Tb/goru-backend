<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Teacher\TeacherLevelRequest;
use App\Exceptions\AlreadyTakenException;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Models\TeacherLevel;
use Illuminate\Http\Request;
use App\Models\Level;

class TeacherLevelController extends Controller
{
    public function getAllTeacherLevel()
    {
        $teacherLevels = TeacherLevel::with(['level:id,name'])
            ->where('user_id', '=', auth()->user()->id)
            ->get(['id', 'level_id']);
        return response()->json([
            'message' => 'Berhasil ambil data jenjang pendidikan ajar guru',
            'data' => $teacherLevels
        ]);
    }

    public function createTeacherLevel(TeacherLevelRequest $teacherLevelRequest)
    {
        $levelId = $teacherLevelRequest->validated('level_id');
        $level = Level::select('id')->find($levelId);
        if (is_null($level)) throw new NotFoundException('Data jenjang pendidikan tidak ditemukan');

        $teacherLevel = TeacherLevel::where([
            'level_id' => $levelId,
            'user_id' => auth()->user()->id
        ])->get(['id'])->count();

        if ($teacherLevel == 1) {
           throw new AlreadyTakenException('Kamu sudah mengambil jenjang pendidikan ini');
        }

        TeacherLevel::create([
            'level_id' => $levelId,
            'user_id' => auth()->user()->id
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Berhasil mengambil jenjang pendidikan'
        ]);
    }

    public function deleteTeacherLevel(string $teacherLevelId)
    {
        $teacherLevel = TeacherLevel::select('id')->find($teacherLevelId);
        if (is_null($teacherLevel)) {
            throw new NotFoundException('Data jenjang pendidikan ajar kamu tidak ditemukan');
        }

        $teacherLevel->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menghapus jenjang pendidikan ajar kamu'
        ], 200);
    }
}
