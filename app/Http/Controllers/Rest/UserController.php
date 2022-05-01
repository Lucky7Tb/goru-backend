<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Admin\Teacher\UpdateRecomendationDateRequest;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\User;

class UserController extends Controller
{
    public function getListTeacher()
    {
        $teacher = User::select('id', 'full_name', 'phone_number', 'email', 'photo_profile', 'recommended_until')
            ->where('role', '=', 'teacher')
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil data user dengan role teacher',
            'data' => $teacher
        ], 200);
    }

    public function updateRecommendationTeacherDate(string $teacherId, UpdateRecomendationDateRequest $request)
    {
        if (!Str::isUuid(($teacherId))) {
            throw new BadRequestException('Format id guru tidak benar');
        }

        $teacher = User::select('id')->find($teacherId);
        if (is_null($teacher)) {
            throw new NotFoundException('Guru tidak ditemukan');
        }

        $recommendationDate = $request->validated('date');
        $teacher->recommended_until = $recommendationDate;
        $teacher->save();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengubah tanggal rekomendasi'
        ], 200);
    }

    public function getListStudent()
    {
        $students = User::select('id', 'full_name', 'phone_number', 'email')
            ->where('role', '=', 'student')
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil data user dengan role student',
            'data' => $students
        ], 200);
    }
}
