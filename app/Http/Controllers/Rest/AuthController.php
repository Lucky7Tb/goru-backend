<?php

namespace App\Http\Controllers\Rest;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $user = Auth::user();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->is_ban) {
                throw new ForbiddenException("Maaf akun anda telah di ban");
                Auth::logout();
            } else {
                $token = $user->createToken(env("APP_NAME"))->plainTextToken;
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
        User::create($userData);
        return response()->json([
            "status" => 201,
            "message" => "Sukses registrasi"
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "status" => 200,
            "message" => "Sukses logout"
        ]);
    }
}
