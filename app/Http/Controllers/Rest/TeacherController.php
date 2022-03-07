<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Teacher\HireTeacherRequest;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Models\TeacherPackage;
use App\Models\ScheduleDetail;
use App\Models\Schedule;
use App\Models\User;

class TeacherController extends Controller
{
    public function hireTeacher(HireTeacherRequest $hireTeacherRequest, string $teacherId)
    {
        $requestedScheduleData = $hireTeacherRequest->validated();

        $teacherPackage = TeacherPackage::select('id', 'package', 'encounter')->find($requestedScheduleData['package_id']);
        if (is_null($teacherPackage)) throw new NotFoundException('Paket guru tidak ditemukan');

        $teacher = User::select('id')->find($teacherId);
        if (is_null($teacher)) throw new NotFoundException('Guru tidak ditemukan');

        $scheduleData = [
           "student_id" => auth()->user()->id,
           "teacher_id" => $teacher->id,
           "teacher_package_id" => $teacherPackage->id,
           "note" => $requestedScheduleData['note'],
        ];

        $schedule = new Schedule();
        if ($teacherPackage->package == TeacherPackage::PERDAY) {
            $scheduleData['from_date'] = $requestedScheduleData['schedules'][0]['date'];
            $scheduleData['to_date'] = $requestedScheduleData['schedules'][0]['date'];
        } else {
            $scheduleData['from_date'] = $requestedScheduleData['schedules'][0]['date'];
            $scheduleData['to_date'] = $requestedScheduleData['schedules'][$teacherPackage->encounter - 1]['date'];
        }

        $schedule = Schedule::create($scheduleData);

        $scheduleDetailData = [];

        for ($index=0; $index < $teacherPackage->encounter; $index++) {
            $scheduleData = $requestedScheduleData['schedules'][$index];
            $scheduleDetailData[] = [
                'schedule_id' => $schedule->id,
                'date' => $scheduleData['date'],
                'from_time' => $scheduleData['from_time'],
                'to_time' => $scheduleData['to_time']
            ];
        }
        ScheduleDetail::create($scheduleDetailData);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menyewa guru, silahkan tunggu konfirmasi dari guru'
        ]);
    }
}
