<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Admin\Wallet\UpdateEvidanceRequestWalletRequest;
use App\Http\Requests\Teacher\Wallet\CreateRequestWalletRequest;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Firebase\FirebaseCloudMessage;
use Kreait\Firebase\Contract\Messaging;
use Illuminate\Support\Facades\Crypt;
use Kreait\Firebase\Contract\Storage;
use App\Http\Controllers\Controller;
use App\Models\TeacherRequestWallet;
use App\Firebase\FirebaseStorage;
use App\Models\User;

class WalletController extends Controller
{

    private $firebaseStorage;
    private $firebaseCloudMessage;

    public function __construct(Storage $storage, Messaging $messaging)
    {
        $this->firebaseStorage = new FirebaseStorage($storage);
        $this->firebaseCloudMessage = new FirebaseCloudMessage($messaging);
    }

    public function getListTeacherRequestWallet()
    {
        $requestWallet = TeacherRequestWallet::select('id', 'user_id', 'bank_name', 'bank_account_number', 'request_ammount', 'evidance', 'created_at')
            ->with([
                'teacher:id,full_name,phone_number'
            ])
            ->when(auth()->user()->role === 'Teacher', function ($query) {
                return $query
                    ->where('user_id', '=', auth()->user()->id)
                    ->orderBy('created_at', 'desc');
            })
            ->when(auth()->user()->role === 'Admin', function ($query) {
                return $query->orderBy('created_at', 'asc');
            })
            ->when(request('status') === 'not_transfer_yet', function ($query) {
                return $query->where('evidance', '=', null);
            })
            ->when(request('status') === 'transfered', function ($query) {
                return $query->where('evidance', '!=', null);
            })
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil list request pencairan dana',
            'data' => $requestWallet
        ], 200);
    }

    public function getOneTeacherRequestWallet(int $requestWalletId)
    {
        $requestWallet = TeacherRequestWallet::select('id', 'user_id', 'bank_name', 'bank_account_number', 'request_ammount', 'evidance')
            ->find($requestWalletId);
        if (is_null($requestWallet) || $requestWallet->user_id !== auth()->user()->id) {
            throw new NotFoundException('Request pencairan tidak ditemukan');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil request pencairan dana',
            'data' => $requestWallet
        ], 200);
    }

    public function getMyWallet()
    {
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil jumlah dana kamu',
            'data' => ['wallet' => auth()->user()->wallet]
        ], 200);
    }

    public function createRequestWallet(CreateRequestWalletRequest $request)
    {
        $requestedWalletData = $request->validated();

        if (auth()->user()->wallet < $requestedWalletData['request_ammount']) {
            throw new BadRequestException('Jumlah saldo yang kamu punya tidak cukup');
        }

        TeacherRequestWallet::create([
            'user_id' => auth()->user()->id,
            'bank_name' => $requestedWalletData['bank_name'],
            'bank_account_number' => Crypt::encryptString($requestedWalletData['bank_account_number']),
            'request_ammount' => $requestedWalletData['request_ammount']
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil membuat permintaan pencairan saldo'
        ], 200);
    }

    public function updateTeacherRequestWalletEvidance(int $requestWalletId, UpdateEvidanceRequestWalletRequest $request)
    {
        $requestWallet = TeacherRequestWallet::select('id', 'user_id', 'request_ammount', 'evidance')
            ->with([
                'teacher:id,device_token'
            ])
            ->find($requestWalletId);

        if (is_null($requestWallet)) {
            throw new NotFoundException('Data request pencairan dana tidak ada');
        }

        if(!is_null($requestWallet->evidance)) {
            throw new BadRequestException('Permintaan pencairan saldo ini sudah ditransfer');
        }

        $evidanceFile = $request->file('evidance');
        $evidanceFileName = $this->firebaseStorage->uploadFile($evidanceFile, 'wallet_transfer_evidances/');
        $requestWallet->evidance = $evidanceFileName;
        $requestWallet->save();

        User::find($requestWallet->user_id)->decrement('wallet', $requestWallet->request_ammount);

        if(!is_null($requestWallet->teacher->device_token))
        {
            $message = CloudMessage::withTarget('token', $requestWallet->teacher->device_token)
                ->withNotification(
                    Notification::create('Pembayaran kamu diterima', 'Sekarang kamu tinggal tunggu guru kamu memberikan link belajarnya deh')
                )
                ->withData([
                    'status' => 'success',
                    'navigate' => 'RequestWalletDetail',
                    'param' => 'requestWalletId',
                    'value' => $requestWallet->id
                ])
                ->withDefaultSounds();

            $this->firebaseCloudMessage->sendNotification($message);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengupload bukti transfer pencairan dana'
        ], 200);
    }
}
