<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Student\HireTeacherRequest;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Models\TeacherPackage;
use App\Models\TeacherDocumentAdditional;
use App\Models\ScheduleDetail;
use App\Models\Schedule;
use App\Models\User;
use App\Firebase\FirebaseStorage;
use Kreait\Firebase\Contract\Storage;

class TeacherController extends Controller
{
    private $firebaseStorage;

    public function __construct(Storage $storage) {
        $this->firebaseStorage = new FirebaseStorage($storage);
    }

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
        ScheduleDetail::insert($scheduleDetailData);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menyewa guru, silahkan tunggu konfirmasi dari guru'
        ]);
    }

    public function getTeacher()
    {
        $getTeachers = User::select('id','full_name','photo_profile','bio')
        ->where('role', '=', 'teacher')
        ->get();

        foreach ($getTeachers as $getTeacher){
            if($getTeacher->photo_profile !== null){
                $getTeacher->photo_profile = "https://firebasestorage.googleapis.com/v0/b/goru-ee0f3.appspot.com/o/photo_profile%2F$getTeacher->photo_profile?alt=media";
            }
        }

        return response()->json([
            'message' => 'Sukses menagambil list guru',
            'data' => $getTeacher
        ], 200);
    }

    public function getDetailTeacher($idTeacher)
    {
        $getTeacher = User::select('id')
        ->find($idTeacher);
        if (is_null($getTeacher)) {
            throw new NotFoundException('data tidak ditemukan');
        }

        $teacherDetails = User::select('id','full_name','bio','identity_photo','photo_profile' )
        ->with([
            'teacherLessonSubject:id,lesson_subject_id,user_id',
            'teacherLessonSubject.lessonSubject:id,name',
            'teacherDocumentAdditional:id,user_id,document'
        ])->find($getTeacher->id);

        if (is_null($teacherDetails->photo_profile)) {
            $teacherDetails->photo_profile = "https://firebasestorage.googleapis.com/v0/b/goru-ee0f3.appspot.com/o/photo_profiles%2Fuser.png?alt=media";
            
        } else {
            $teacherDetails->photo_profile = "https://firebasestorage.googleapis.com/v0/b/goru-ee0f3.appspot.com/o/photo_profiles%2F".$teacherDetails->teacher->photo_profile."?alt=media";
        }

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil detail guru',
            'data' => $teacherDetails
        ]);

    }

    public function getRecomendedTeacher()
    {
        $getTeachersRecomend = User::select('id','is_recomended')
        ->where('role', '=', 'teacher')
        ->where('is_recomended' , true)
        ->get();

        return response()->json([
            'message' => 'Sukses menagambil rekomendasi guru',
            'data' => $getTeachersRecomend
        ], 200);
    }

    public function getTeacherByFilter()
    {
        
    }
}
