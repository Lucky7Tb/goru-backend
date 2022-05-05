<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Teacher\Package\TeacherPackageRequest;
use App\Exceptions\AlreadyTakenException;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Models\TeacherPackage;

class TeacherPackageController extends Controller
{
    public function getAllTeacherPackage()
    {
        $teacherPackages = TeacherPackage::select('id', 'package', 'price_per_hour', 'encounter', 'is_active')
            ->where('user_id', '=', auth()->user()->id)
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil data paket guru',
            'data' => $teacherPackages
        ], 200);
    }

    public function getOneTeacherPackage(string $teacherPackageId)
    {
        $teacherPackage = TeacherPackage::select(['id', 'package', 'price_per_hour', 'encounter', 'is_active'])
            ->find($teacherPackageId);

        if (is_null($teacherPackage)) {
            throw new NotFoundException('Data paket tidak ditemukan');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil data paket guru',
            'data' => $teacherPackage
        ], 200);
    }

    public function createTeacherPackage(TeacherPackageRequest $teacherPackageRequest)
    {
        $teacherPackageData = $teacherPackageRequest->validated();
        $teacherPackage = TeacherPackage::where([
            'package' => $teacherPackageData['package'],
            'user_id' => auth()->user()->id
        ])->get(['id'])->count();

        if ($teacherPackage == 1) {
            throw new AlreadyTakenException('Kamu udah ada paket ini');
        }

        TeacherPackage::create([
            'user_id' => auth()->user()->id,
            'package' => $teacherPackageData['package'],
            'price_per_hour' => $teacherPackageData['price_per_hour'],
            'encounter' => $teacherPackageData['package'] == TeacherPackage::PERDAY ? 1 : $teacherPackageData['encounter'],
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Berhasil nambah paket ajar kamu'
        ], 201);
    }

    public function updateTeacherPackage(string $teacherPackageId, TeacherPackageRequest $teacherPackageRequest)
    {
        $updatedTeacherPackageData = $teacherPackageRequest->validated();
        $teacherPackage = TeacherPackage::select('id', 'package')->find($teacherPackageId);

        if (is_null($teacherPackage)) {
            throw new NotFoundException('Data paket tidak ditemukan');
        }

        if ($teacherPackage->package != TeacherPackage::PERDAY) {
            $teacherPackage->encounter = $updatedTeacherPackageData['encounter'];
        }
        $teacherPackage->price_per_hour = $updatedTeacherPackageData['price_per_hour'];
        $teacherPackage->save();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengubah informasi paket kamu'
        ]);
    }

    public function toggleStatusTeacherPackage(string $teacherPackageId)
    {
        $teacherPackage = TeacherPackage::select('id', 'is_active')->find($teacherPackageId);

        if (is_null($teacherPackage)) {
            throw new NotFoundException('Data paket tidak ditemukan');
        }

        $teacherPackage->is_active = !$teacherPackage->is_active;
        $teacherPackage->save();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengubah status paket kamu'
        ]);
    }
}
