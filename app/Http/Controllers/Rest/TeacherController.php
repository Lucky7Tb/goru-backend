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
        $teachers = User::select('id','full_name','photo_profile','bio')
        ->with([
            'teacherLessonSubject:user_id,lesson_subject_id',
            'teacherLessonSubject.lessonSubject:id,name',
        ])
        ->when(request('name'), function ($query) {
            $seacrTerm = request('name');
            return $query->orWhere('full_name', 'ILIKE', "%{$seacrTerm}%");
        })
        ->when(request('lesson_subject_id'), function ($query) {
            return $query->whereHas('teacherLessonSubject', function ($q) {
                return $q->where('lesson_subject_id', request('lesson_subject_id'));
            });
        })
        ->when(request('level_id'), function ($query) {
            return $query->whereHas('teacherLevel', function ($q) {
                return $q->where('level_id', request('level_id')); 
            });
        })
        ->where('role', '=', 'teacher')
        ->get();

        return response()->json([
            'message' => 'Sukses mengambil list guru',
            'data' => $teachers
        ], 200);
    }

    public function getDetailTeacher(string $idTeacher)
    {
        $getTeacher = User::select('id')->find($idTeacher);
        if (is_null($getTeacher)) {
            throw new NotFoundException('data tidak ditemukan');
        }

        $teacherDetails = User::select('id','full_name','bio','identity_photo','photo_profile' )
        ->with([
            'teacherLessonSubject:id,lesson_subject_id,user_id',
            'teacherLessonSubject.lessonSubject:id,name',
            'teacherLevel:user_id,level_id',
            'teacherLevel.level:id,name',
            'teacherDocumentAdditional:id,user_id,document',
        ])->find($getTeacher->id);

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
