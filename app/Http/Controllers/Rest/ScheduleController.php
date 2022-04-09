<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Teacher\UpdateScheduleMeetEvidanceRequest;
use App\Http\Requests\Teacher\UpdateScheduleMeetLinkRequest;
use App\Http\Requests\Teacher\UpdateTeacherScheduleRequest;
use App\Http\Requests\Student\UpdateStudentScheduleRequest;
use App\Exceptions\NotAcceptableException;
use App\Exceptions\NotFoundException;
use Kreait\Firebase\Contract\Storage;
use App\Http\Controllers\Controller;
use App\Firebase\FirebaseStorage;
use App\Models\TeacherPackage;
use App\Models\ScheduleDetail;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    private $firebaseStorage;

    public function __construct(Storage $storage)
    {
        $this->firebaseStorage = new FirebaseStorage($storage);
    }

    public function getTeacherSchedule()
    {
        $schedules = Schedule::select('id', 'student_id', 'teacher_package_id', 'from_date', 'to_date', 'status', 'note')
            ->with([
                'package:id,package',
                'student:id,full_name,phone_number'
            ])
            ->where('teacher_id', auth()->user()->id)
            ->when(request('status'), function ($query) {
                return $query->where('status', request('status'));
            })
            ->when(request('package'), function ($query) {
                return $query->whereHas('package', function ($q) {
                    return $q->where('package', request('package'));
                });
            })
            ->orderBy('created_at', 'ASC')
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil jadwal kamu',
            'data' => $schedules
        ]);
    }

    public function getTeacherScheduleDetail(string $scheduleId)
    {
        $schedule = Schedule::select('id')->find($scheduleId);
        if (is_null($schedule)) {
            throw new NotFoundException('Jadwal anda tidak ditemukan');
        }

        $scheduleDetails = ScheduleDetail::select('id', 'schedule_id', 'date', 'from_time', 'to_time', 'status', 'note', 'meet_evidance', 'meet_link')
            ->where('schedule_id', $schedule->id)
            ->when(request('status'), function ($query) {
                return $query->where('status', request('status'));
            })
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil detail dari jadwal kamu',
            'data' => $scheduleDetails
        ]);
    }

    public function updateTeacherScheduleDetail(string $scheduleId, string $scheduleDetailId, UpdateTeacherScheduleRequest $request)
    {
        $schedule = Schedule::select('id', 'student_id', 'teacher_id', 'teacher_package_id')->find($scheduleId);
        if (is_null($schedule)) {
            throw new NotFoundException('Jadwal anda tidak ditemukan');
        }

        $scheduleDetails = ScheduleDetail::select('id')->find($scheduleDetailId);
        if (is_null($scheduleDetails)) {
            throw new NotFoundException('Detail jadwal anda tidak ditemukan');
        }

        $updatedScheduleDetailData = $request->validated();
        $scheduleDetails->note = $updatedScheduleDetailData['note'];
        $scheduleDetails->status = $updatedScheduleDetailData['status'];
        $scheduleDetails->save();

        if ($updatedScheduleDetailData['status'] !== 'accepted') {
            Schedule::find($scheduleId)->update([
                'status' => $updatedScheduleDetailData['status']
            ]);
        }

        $rejectedScheduleDetail = ScheduleDetail::where([
            'schedule_id' => $scheduleId,
            'status' => 'rejected'
        ])->count();
        $inReviewScheduleDetail = ScheduleDetail::where([
            'schedule_id' => $scheduleId,
            'status' => 'in_review'
        ])->count();

        if ($rejectedScheduleDetail == 0 && $inReviewScheduleDetail == 0) {
            Schedule::find($scheduleId)->update([
                'status' => 'accepted'
            ]);
            $scheduleDetails = ScheduleDetail::select('id', 'from_time', 'to_time')
                ->where('schedule_id', $scheduleId)
                ->get();
            $teacherPackage = TeacherPackage::select('price_per_hour', 'encounter')
                ->find($schedule->teacher_package_id);
            $totalPrice = 0;

            for ($i = 0; $i < $teacherPackage->encounter; $i++) {
                $hour = $scheduleDetails[$i]['to_time']->diffInHours($scheduleDetails[$i]['from_time']);
                $totalPrice += $hour * $teacherPackage->price_per_hour;
            }

            $adminPrice = $totalPrice * (10/100);
            $totalPrice += $adminPrice; 

            Transaction::create([
                'student_id' => $schedule->student_id,
                'teacher_id' => $schedule->teacher_id,
                'teacher_package_id' => $schedule->teacher_package_id,
                'schedule_id' => $schedule->id,
                'price_per_hour' => $teacherPackage->price_per_hour,
                'total_price' => $totalPrice,
                'status' => 'not_paid_yet'
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Sukses mengubah status jadwal'
        ], 200);
    }

    public function updateScheduleMeetLink(string $scheduleId, string $scheduleDetailId, UpdateScheduleMeetLinkRequest $request)
    {
        $meetLink = $request->validated('meet_link');

        $schedule = Schedule::select('id')
            ->with('transaction:id,schedule_id,status')
            ->find($scheduleId);
        if (is_null($schedule)) {
            throw new NotFoundException('Jadwal anda tidak ditemukan');
        }

        $scheduleDetails = ScheduleDetail::select('id')
            ->find($scheduleDetailId);
        if (is_null($scheduleDetails)) {
            throw new NotFoundException('Detail jadwal anda tidak ditemukan');
        }

        switch ($schedule->transaction->status) {
            case 'not_paid_yet':
                throw new NotAcceptableException('Siswa belum melakukan pembayaran');
            case 'paid':
                $scheduleDetails->update([
                    'meet_link' => $meetLink
                ]);
                break;
            default:
                throw new NotAcceptableException('Transaksi siswa sedang bermasalah, mohon tunggu');
                break;
        }

        return response()->json([
            'status' => 200,
            'message' => 'Sukses mengubah meeting link'
        ], 200);
    }

    public function updateScheduleMeetEvidance(string $scheduleId, string $scheduleDetailId, UpdateScheduleMeetEvidanceRequest $request)
    {

        $schedule = Schedule::select('id')
            ->find($scheduleId);
        if (is_null($schedule)) {
            throw new NotFoundException('Jadwal anda tidak ditemukan');
        }

        $scheduleDetails = ScheduleDetail::select('id', 'date')
            ->find($scheduleDetailId);
        if (is_null($scheduleDetails)) {
            throw new NotFoundException('Detail jadwal anda tidak ditemukan');
        }

        if (today() < $scheduleDetails->date) {
            throw new NotAcceptableException('Anda hanya bisa menambahkan bukti meet jika jadwal pembelajaran 1 hari setelah jadwal');
        }

        $meetEvidance = $request->file('meet_evidance');
        $fileName = $this->firebaseStorage->uploadFile($meetEvidance, 'meet_evidances/');
        $scheduleDetails->update([
            'meet_evidance' => $fileName
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Sukses mengubah bukti meeting'
        ], 200);
    }

    public function getStudentSchedule()
    {
        $schedules = Schedule::select('id', 'student_id', 'teacher_id', 'teacher_package_id', 'from_date', 'to_date', 'status', 'note')
            ->with([
                'package:id,package',
                'teacher:id,full_name,phone_number,photo_profile'
            ])
            ->where('student_id', auth()->user()->id)
            ->when(request('status'), function ($query) {
                return $query->where('status', request('status'));
            })
            ->when(request('package'), function ($query) {
                return $query->whereHas('package', function ($q) {
                    return $q->where('package', request('package'));
                });
            })
            ->orderBy('created_at', 'ASC')
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil jadwal kamu',
            'data' => $schedules
        ]);
    }

    public function getStudentScheduleDetail(string $scheduleId)
    {
        $schedule = Schedule::select('id')->find($scheduleId);
        if (is_null($schedule)) {
            throw new NotFoundException('Jadwal anda tidak ditemukan');
        }

        $scheduleDetails = Schedule::select('id', 'teacher_id', 'status')
            ->with([
                'scheduleDetail:id,schedule_id,date,from_time,to_time,note,meet_link,status',
                'transaction:id,schedule_id,status',
                'teacher:id,full_name,phone_number,photo_profile',
                'teacher.teacherLessonSubject:id,lesson_subject_id,user_id',
                'teacher.teacherLessonSubject.lessonSubject:id,name'
            ])
            ->find($schedule->id);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil detail dari jadwal kamu',
            'data' => $scheduleDetails
        ]);
    }

    public function updateStudentScheduleDetail(string $scheduleId, string $scheduleDetailId, UpdateStudentScheduleRequest $request)
    {
        $schedule = Schedule::select('id', 'student_id', 'teacher_id', 'teacher_package_id')->find($scheduleId);
        if (is_null($schedule)) {
            throw new NotFoundException('Jadwal anda tidak ditemukan');
        }

        $scheduleDetails = ScheduleDetail::select('id')->find($scheduleDetailId);
        if (is_null($scheduleDetails)) {
            throw new NotFoundException('Detail jadwal anda tidak ditemukan');
        }

        $updatedScheduleDetailData = $request->validated();
        $updatedScheduleDetailData['status'] = 'in_review';
        $scheduleDetails->update($updatedScheduleDetailData);
        $schedule->update([
            'from_date' => $updatedScheduleDetailData['date'],
            'to_date' => $updatedScheduleDetailData['date'],
            'status' => 'in_review'
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Sukses mengubah jadwal anda'
        ], 200);
    }
}
