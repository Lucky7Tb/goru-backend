<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Admin\ApplicationBankAccount\UpdateApplicationBankAccountLogoRequest;
use App\Http\Requests\Admin\ApplicationBankAccount\CreateApplicationBankAccountRequest;
use App\Http\Requests\Admin\ApplicationBankAccount\UpdateApplicationBankAccountRequest;
use App\Models\ApplicationBankAccount;
use App\Exceptions\NotFoundException;
use Kreait\Firebase\Contract\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use App\Firebase\FirebaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplicationBankAccountController extends Controller
{
    private $firebaseStorage;

    public function __construct(Storage $storage)
    {
        $this->firebaseStorage = new FirebaseStorage($storage);
    }

    public function getAllBankAccount()
    {
        $bankAccounts = ApplicationBankAccount::select('id', 'name', 'number', 'alias', 'bank_logo', 'is_active')
            ->when(request('is_active'), function ($query) {
                return $query->where('is_active', request('is_active'));
            })
            ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil akun bank',
            'data' => $bankAccounts
        ], 200);
    }

    public function getOneBankAccount(string $bankAccountId)
    {
        if (!Str::isUuid($bankAccountId)) {
            throw new NotFoundException('Akun bank tidak ditemukan');
        }

        $bankAccount = ApplicationBankAccount::select('id', 'name', 'number', 'alias', 'bank_logo', 'is_active')->find($bankAccountId);

        if (is_null($bankAccount)) {
            throw new NotFoundException('Akun bank tidak ditemukan');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil akun bank',
            'data' => $bankAccount
        ], 200);
    }

    public function createBankAccount(CreateApplicationBankAccountRequest $request)
    {
        $bankData = $request->validated();
        $bankLogo = $request->file('bank_logo');

        $bankLogoFileName = $this->firebaseStorage->uploadFile($bankLogo, 'bank_logos/');

        $bankData['number'] = Crypt::encryptString($bankData['number']);
        $bankData['bank_logo'] = $bankLogoFileName;
        $bankData['is_active'] = true;
        ApplicationBankAccount::create($bankData);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menambahkan akun bank'
        ], 200);
    }


    public function updateBankAccount(string $bankAccountId, UpdateApplicationBankAccountRequest $request)
    {
        if (!Str::isUuid($bankAccountId)) {
            throw new NotFoundException('Akun bank tidak ditemukan');
        }

        $bankAccount = ApplicationBankAccount::select('id')->find($bankAccountId);
        if (is_null($bankAccount)) {
            throw new NotFoundException('Akun bank tidak ditemukan');
        }

        $updatedBankAccounData = $request->validated();
        $updatedBankAccounData['number'] = Crypt::encryptString($updatedBankAccounData['number']);
        $bankAccount->update($updatedBankAccounData);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengubah informasi akun bank'
        ], 200);
    }

    public function updateBankAccountLogo(string $bankAccountId, UpdateApplicationBankAccountLogoRequest $request)
    {
        if (!Str::isUuid($bankAccountId)) {
            throw new NotFoundException('Akun bank tidak ditemukan');
        }

        $bankAccount = ApplicationBankAccount::select('id', 'bank_logo')->find($bankAccountId);
        if (is_null($bankAccount)) {
            throw new NotFoundException('Akun bank tidak ditemukan');
        }

        $bankLogo = $request->file('bank_logo');
        $bankLogoFileName = $this->firebaseStorage->updateFile($bankLogo, 'bank_logos/', $bankAccount->bank_logo);
        $bankAccount->update([
            'bank_logo' => $bankLogoFileName
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengubah logo akun bank'
        ], 200);
    }
}
