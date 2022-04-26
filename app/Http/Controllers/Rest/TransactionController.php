<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Student\Transaction\UploadTransactionEvidanceRequest;
use App\Http\Requests\Student\Transaction\UpdateTransaferMethodRequest;
use App\Http\Requests\Admin\UpdateScheduleStatusRequest;
use Illuminate\Support\Facades\Crypt;
use Kreait\Firebase\Contract\Storage;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Firebase\FirebaseStorage;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    private $firebaseStorage;

    public function __construct(Storage $storage)
    {
        $this->firebaseStorage = new FirebaseStorage($storage);
    }

    public function getAllTransaction()
    {
        $transactions = Transaction::select('id', 'teacher_package_id', 'teacher_id', 'application_bank_account_id', 'total_price', 'status', 'updated_at')
            ->with([
                'teacherPackage:id,package',
                'teacher:id,full_name,phone_number,photo_profile',
                'applicationBank:id,name,number',
            ])
            ->orderBy('created_at', 'ASC')
            ->when(request('status'), function ($query) {
                return $query->where('status', '=', request('status'));
            })
            ->when(auth()->user()->role == 'Student', function ($query) {
                return $query->where('student_id', '=', auth()->user()->id);
            })
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mendapatkan data transaksi',
            'data' => $transactions
        ], 200);
    }

    public function getOneTransaction(string $transactionId)
    {
        $transaction = Transaction::with([
            'student:id,full_name,phone_number,photo_profile',
            'teacher:id,full_name,phone_number,photo_profile',
            'teacherPackage:id,package',
            'schedule:id,from_date,to_date',
            'schedule.scheduleDetail:schedule_id,from_time,to_time',
            'applicationBank:id,name,number,bank_logo',
        ])
            ->find($transactionId);
        $transaction->study_hour = 0;

        if (is_null($transaction)) {
            throw new NotFoundException('Transaksi tidak ditemukan');
        }

        foreach ($transaction->schedule->scheduleDetail as $detail) {
            $hour = $detail->to_time->diffInHours($detail->from_time);
            $transaction->study_hour += $hour;
        }

        $transaction->admin_price = $transaction->total_price - ($transaction->study_hour * $transaction->price_per_hour);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil detail transaksi',
            'data' => $transaction
        ], 200);
    }

    public function updateTransactionStatus(string $transactionId, UpdateScheduleStatusRequest $request)
    {
        $updatedTransactionData = $request->validated();

        $transaction = Transaction::select('id')->find($transactionId);
        if (is_null($transaction)) {
            throw new NotFoundException('Transaksi tidak ditemukan');
        }

        $transaction->update([
            'status' => $updatedTransactionData['status'],
            'note_evidance' => $updatedTransactionData['note_evidance'],
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengupdate status transaksi'
        ]);
    }

    public function changeTransferMethodStudent(string $transactionId, UpdateTransaferMethodRequest $request) 
    {
        $applicationBankAccountId = $request->validated('application_bank_account_id');

        $transaction = Transaction::select('id')->find($transactionId);
        if (is_null($transaction)) {
            throw new NotFoundException('Transaksi tidak ditemukan');
        }

        $transaction->update(['application_bank_account_id' => $applicationBankAccountId]);

        return response()
            ->json([
                'status' => 200,
                'message' => 'Berhasil memilih bank untuk transfer'
        ], 200);
    }

    public function uploadTransferEvidanceStudent(string $transactionId, UploadTransactionEvidanceRequest $request)
    {
        $transaction = Transaction::select('id', 'evidance')->find($transactionId);
        if (is_null($transaction)) {
            throw new NotFoundException('Transaksi tidak ditemukan');
        }

        $evidance = $request->file('evidance');
        $evidanceFileName = $this->firebaseStorage->uploadFile($evidance, 'transaction_evidances/');
        $transaction->update([
            'evidance' => $evidanceFileName,
            'status' => 'in_review'
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengupload bukti trasaksi'
        ], 200);
    }
}
