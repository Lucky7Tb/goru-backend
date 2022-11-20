<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Teacher\Schedule\UpdateScheduleMeetEvidanceRequest;
use App\Http\Requests\Teacher\Schedule\UpdateScheduleMeetLinkRequest;
use App\Http\Requests\Student\Schedule\UpdateStudentScheduleRequest;
use App\Http\Requests\Teacher\Schedule\UpdateTeacherScheduleRequest;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Exceptions\NotAcceptableException;
use Kreait\Firebase\Contract\Messaging;
use App\Firebase\FirebaseCloudMessage;
use App\Exceptions\NotFoundException;
use Kreait\Firebase\Contract\Storage;
use App\Http\Controllers\Controller;
use App\Firebase\FirebaseStorage;
use App\Models\TeacherPackage;
use App\Models\ScheduleDetail;
use App\Models\Transaction;
use App\Models\Schedule;
use App\Models\User;

class ScheduleController extends Controller
{
    private $firebaseStorage;
    private $firebaseCloudMessage;

    public function __construct(Storage $storage, Messaging $messaging)
    {
        $this->firebaseStorage = new FirebaseStorage($storage);
        $this->firebaseCloudMessage = new FirebaseCloudMessage($messaging);
    }

    public function getTeacherSchedule()
    {
        $schedules = Schedule::select('id', 'student_id', 'teacher_package_id', 'from_date', 'to_date', 'status', 'note')
            ->with([
                'package:id,package',
                'student:id,full_name,phone_number,photo_profile'
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
            ->with([
                'schedule:id,note',
                'schedule.transaction:id,schedule_id,status'
            ])
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
        $message = null;
        $schedule = Schedule::select('id', 'student_id', 'teacher_id', 'teacher_package_id')
            ->with([
                'student:id,device_token'
            ])
            ->find($scheduleId);
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

            if(!is_null($schedule->student->device_token)) {
                $message = CloudMessage::withTarget('token', $schedule->student->device_token)
                    ->withNotification(
                        Notification::create('Jadwal kamu ditolak nih', 'Ayo atur ulang jadwal kamu')
                    )
                    ->withData([
                        'status' => 'error',
                        'navigate' => 'ScheduleDetail',
                        'param' => 'scheduleId',
                        'value' => $schedule->id
                    ])
                    ->withDefaultSounds();
            }
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

            $adminPrice = $totalPrice * (10 / 100);
            $totalPrice += $adminPrice;

            $transaction = Transaction::create([
                'student_id' => $schedule->student_id,
                'teacher_id' => $schedule->teacher_id,
                'teacher_package_id' => $schedule->teacher_package_id,
                'schedule_id' => $schedule->id,
                'price_per_hour' => $teacherPackage->price_per_hour,
                'admin_price' => $adminPrice,
                'total_price' => $totalPrice,
                'status' => 'not_paid_yet'
            ]);

            if(!is_null($schedule->student->device_token)) {
                $message = CloudMessage::withTarget('token', $schedule->student->device_token)
                    ->withNotification(
                        Notification::create('Jadwal kamu diterima!', 'Ayo sekarang lanjutkan ke pembayaran')
                    )
                    ->withData([
                        'status' => 'success',
                        'navigate' => 'TransactionDetail',
                        'param' => 'transactionId',
                        'value' => $transaction->id
                    ])->withDefaultSounds();
            }
        }

        if(!is_null($schedule->student->device_token)) {
            $this->firebaseCloudMessage->sendNotification($message);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Sukses mengubah status jadwal'
        ], 200);
    }

    public function updateScheduleMeetLink(string $scheduleId, string $scheduleDetailId, UpdateScheduleMeetLinkRequest $request)
    {
        $meetLink = $request->validated('meet_link');

        $schedule = Schedule::select('id', 'student_id')
            ->with([
                'transaction:id,schedule_id,status',
                'student:id,device_token'
            ])
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
                if(!is_null($schedule->student->device_token)) {
                    $message = CloudMessage::withTarget('token', $schedule->student->device_token)
                        ->withNotification(
                            Notification::create('Link pembelajaran kamu sudah ada', 'Link belajar kamu tinggal disalin deh')
                        )
                        ->withData([
                            'status' => 'success',
                            'navigate' => 'ScheduleDetail',
                            'param' => 'scheduleId',
                            'value' => $schedule->id
                        ])
                        ->withDefaultSounds();
                    $this->firebaseCloudMessage->sendNotification($message);
                }
                break;
            default:
                throw new NotAcceptableException('Transaksi siswa sedang bermasalah, mohon tunggu');
                break;
        }

        return response()->json([
            'status' => 200,
            'message' => 'Sukses mengubah link pembelajaran'
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

        $scheduleDetailMeetEvidance = ScheduleDetail::select('id')
            ->where('schedule_id', '=', $scheduleId)
            ->where('meet_evidance', '=', null)
            ->count();

        if ($scheduleDetailMeetEvidance == 0) {
            $transaction = Transaction::select('id', 'schedule_id', 'price_per_hour')
                ->with([
                    'schedule:id',
                    'schedule.scheduleDetail:schedule_id,from_time,to_time',
                ])
                ->where('schedule_id', '=', $scheduleId)->first();
            $studyHour = 0;

            foreach ($transaction->schedule->scheduleDetail as $detail) {
                $hour = $detail->to_time->diffInHours($detail->from_time);
                $studyHour += $hour;
            }

            $teacherWalletPrice = $studyHour * $transaction->price_per_hour;

            User::find(auth()->user()->id)->increment('wallet', $teacherWalletPrice);
        }

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

        $scheduleDetails = Schedule::select('id', 'teacher_id', 'status', 'is_already_feedback')
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
        $schedule = Schedule::select('id', 'student_id', 'teacher_id', 'teacher_package_id')
            ->with([
                'teacher:id,device_token'
            ])
            ->find($scheduleId);
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

        if (!is_null($schedule->teacher->device_token)) {
            $message = CloudMessage::withTarget('token', $schedule->teacher->device_token)
                ->withNotification(
                    Notification::create('Murid kamu sudah ngajuin jadwal yang baru', 'Ayo cek kembali jadwalnya')
                )
                ->withData([
                    'status' => 'success',
                    'navigate' => 'ScheduleDetail',
                    'param' => 'scheduleId',
                    'value' => $schedule->id
                ])
                ->withDefaultSounds();
            $this->firebaseCloudMessage->sendNotification($message);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Sukses mengubah jadwal anda'
        ], 200);
    }
}
