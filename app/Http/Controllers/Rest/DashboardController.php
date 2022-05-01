<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    public function getTotalUserAndTransaction()
    {
        $totalTransaction = Transaction::count();
        $totalUser = User::count();
        $totalStudent = User::where('role', '=', 'student')->count();
        $totalTeacher = User::where('role', '=', 'teacher')->count();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mendapatkan status total',
            'data' => [
                'total_transaction' => $totalTransaction,
                'total_user' => $totalUser,
                'total_student' => $totalStudent,
                'total_teacher' => $totalTeacher,
            ]
        ]);
    }
}
