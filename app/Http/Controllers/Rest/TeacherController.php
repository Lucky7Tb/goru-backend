<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Student\Teacher\HireTeacherRequest;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Contract\Messaging;
use App\Firebase\FirebaseCloudMessage;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Models\ScheduleDetail;
use App\Models\TeacherPackage;
use App\Models\Transaction;
use App\Models\Schedule;
use App\Models\User;

class TeacherController extends Controller
{

    private $firebaseCloudMessage;

    public function __construct(Messaging $messaging)
    {
        $this->firebaseCloudMessage = new FirebaseCloudMessage($messaging);
    }

    public function hireTeacher(HireTeacherRequest $hireTeacherRequest, string $teacherId)
    {
        $requestedScheduleData = $hireTeacherRequest->validated();

        $teacherPackage = TeacherPackage::select('id', 'package', 'encounter')->find($requestedScheduleData['package_id']);
        if (is_null($teacherPackage)) throw new NotFoundException('Paket guru tidak ditemukan');

        $teacher = User::select('id', 'device_token')->find($teacherId);
        if (is_null($teacher)) throw new NotFoundException('Guru tidak ditemukan');

        $scheduleData = [
           "student_id" => auth()->user()->id,
           "teacher_id" => $teacher->id,
           "teacher_package_id" => $teacherPackage->id,
           "note" => $requestedScheduleData['note'],
        ];

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

        if (!is_null($teacher->device_token)) {
            $message = CloudMessage::withTarget('token', $teacher->device_token)
                ->withNotification(
                    Notification::create('Ada yang mau belajar sama kamu nih', 'Coba cek tanggal yang diajukan oleh siswanya yu')
                )
                ->withData([
                    'status' => 'info',
                    'navigate' => 'ScheduleDetail',
                    'param' => 'scheduleId',
                    'value' => $schedule->id
                ])
                ->withDefaultSounds();

            $this->firebaseCloudMessage->sendNotification($message);
        }

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
                if(request('level_id') !== "")
                {
                    return $q->where('level_id', request('level_id'));                    
                }

                return $q->where('level_id', '!=', null);
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
            throw new NotFoundException('Guru tidak ditemukan');
        }

        $teacherDetails = User::select('id','full_name','bio','photo_profile' )
        ->with([
            'teacherLessonSubject:id,lesson_subject_id,user_id',
            'teacherLessonSubject.lessonSubject:id,name,thumbnail',
            'teacherLevel:user_id,level_id',
            'teacherLevel.level:id,name',
            'teacherDocumentAdditional:id,user_id,document',
            'package:id,user_id,package,price_per_hour',
            'teacherComments:id,student_id,teacher_id,comment',
            'teacherComments.student:id,full_name,photo_profile'
        ])
        ->find($getTeacher->id);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil detail guru',
            'data' => $teacherDetails
        ]);

    }

    public function getRecomendedTeacher()
    {
        $recommendedTeacher = User::select('id','full_name','photo_profile','bio')
            ->with([
                'teacherLessonSubject:user_id,lesson_subject_id',
                'teacherLessonSubject.lessonSubject:id,name',
            ])
            ->where('role', '=', 'teacher')
            ->where('recommended_until', '>=', today()->format('Y-m-d'))
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Sukses mengambil rekomendasi guru',
            'data' => $recommendedTeacher
        ], 200);
    }

    public function getLastHireTeacher()
    {
        $lastHireTeacher = Transaction::select('id', 'teacher_id')
            ->with([
                'teacher:id,full_name,photo_profile'
            ])
            ->where([
                'status' => 'paid',
                'student_id' => auth()->user()->id
            ])
            ->limit(5)
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Sukses mengambil 5 guru yang pernah di rekrut',
            'data' => $lastHireTeacher
        ]);
    }
}
