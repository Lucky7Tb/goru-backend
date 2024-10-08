<?php

namespace App\Http\Controllers\Rest;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TeacherPackage;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $deviceToken = null;
        if (isset($credentials['device_token'])) {
            $deviceToken = $credentials['device_token'];
            unset($credentials['device_token']);
        }
        $user = Auth::user();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->is_ban) {
                throw new ForbiddenException("Maaf akun anda telah di ban");
                Auth::logout();
            } else {
                $token = $user->createToken(env("APP_NAME"))->plainTextToken;
                if ($user->role == "Student" || $user->role == "Teacher") {
                    User::find($user->id)->update([
                        'device_token' => $deviceToken
                    ]);
                }
                return response()->json([
                    "status" => 200,
                    "message" => "Sukses login",
                    "data" => [
                        "token" => $token,
                        "user" => $user
                    ]
                ]);
            }
        }

        throw new NotFoundException("Akun tidak ditemukan");
    }

    public function register(RegisterRequest $request)
    {
        $userData = $request->validated();
        $userData["password"] = bcrypt($userData["password"]);
        $createdUser = User::create($userData);

        if ($userData['role'] == 'teacher') {
            TeacherPackage::create([
                'user_id' => $createdUser->id,
                'package' => 'per_day',
                'price_per_hour' => 50000,
            ]);
        }

        return response()->json([
            "status" => 201,
            "message" => "Sukses registrasi"
        ], 201);
    }

    public function logout(Request $request)
    {
        User::find($request->user()->id)->update([
            'device_token' => null
        ]);
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "status" => 200,
            "message" => "Sukses logout"
        ]);
    }
}
