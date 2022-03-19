<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Admin\UpdateScheduleStatusRequest;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function getAllTransaction()
    {
        $transactions = Transaction::select('id', 'teacher_package_id', 'application_bank_account_id', 'total_price', 'evidance', 'status', 'updated_at')
            ->with([
                'teacherPackage:id,package',
                'applicationBank:id,name,number'
            ])
            ->orderBy('created_at', 'ASC')
            ->when(request('status'), function($query) {
                return $query->where('status', '=', request('status'));
            })
            ->get();

        foreach ($transactions as $transaction) {
            if (!is_null($transaction->evidance)) {
                $transaction->evidance = "https://firebasestorage.googleapis.com/v0/b/goru-ee0f3.appspot.com/o/meet_evidances%2F$transaction->evidance?alt=media";
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mendapatkan data transaksi',
            'data' => $transactions
        ], 200);
    }

    public function getOneTransaction(string $transactionId)
    {
        $transaction = Transaction::with([
            'student:id,full_name',
            'teacher:id,full_name',
            'teacherPackage:id,package',
            'schedule:id,from_date,to_date',
            'applicationBank:id,name,number,bank_logo',
        ])
            ->find($transactionId);

        if (is_null($transaction)) {
            throw new NotFoundException('Transaksi tidak ditemukan');
        }

        if (!is_null($transaction->evidance)) {
            $transaction->evidance = "https://firebasestorage.googleapis.com/v0/b/goru-ee0f3.appspot.com/o/meet_evidances%2F$transaction->evidance?alt=media";
        }

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
}
