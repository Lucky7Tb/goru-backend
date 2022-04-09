<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Profile\changePhotoProfileRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\ChangeProfileRequest;
use App\Http\Requests\Profile\ChangeBioRequest;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use Kreait\Firebase\Contract\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Firebase\FirebaseStorage;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    private $firebaseStorage;

    public function __construct(Storage $storage)
    {
        $this->firebaseStorage = new FirebaseStorage($storage);
    }

    public function changeProfile(ChangeProfileRequest $request)
    {
        $user = User::find(auth()->user()->id);
        if (is_null($user)) {
            throw new NotFoundException('Akun tidak ditemukan');
        }
        $user->full_name = $request->validated('full_name');
        $user->phone_number = $request->validated('phone_number');
        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengubah profil',
            'data' => $user
        ], 200);
    }

    public function changeBio(ChangeBioRequest $request)
    {
        $user = User::find(auth()->user()->id);
        if (is_null($user)) {
            throw new NotFoundException('Akun tidak ditemukan');
        }
        $user->bio = $request->validated('bio');
        $user->save();
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengubah bio anda'
        ], 200);
    }

    public function changePhotoProfile(changePhotoProfileRequest $request)
    {
        $user = User::select('id', 'photo_profile')->find(auth()->user()->id);
        if (is_null($user)) {
            throw new NotFoundException('Akun tidak ditemukan');
        }

        $photoProfile = $request->file('photo_profile');
        $photoProfileFileName = $user->photo_profile;
        if (is_null($photoProfileFileName)) {
            $photoProfileFileName = $this->firebaseStorage->uploadFile($photoProfile, 'photo_profiles/');
        } else {
            $photoProfileFileName = $this->firebaseStorage->updateFile($photoProfile, 'photo_profiles/', $user->photo_profile);
        }

        $user->photo_profile = $photoProfileFileName;
        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengubah foto profil anda',
            'data' => [
                "photo_profile" => $photoProfileFileName
            ]
        ], 200);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = User::select('id', 'password')->find(auth()->user()->id);
        if (is_null($user)) {
            throw new NotFoundException('Akun tidak ditemukan');
        }

        $oldPassword = $request->validated('old_password');
        $newPassword = $request->validated('new_password');

        if (!Hash::check($oldPassword, $user->password)) {
            throw new BadRequestException('Password lama anda salah!');
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengubah password anda'
        ], 200);
    }
}
