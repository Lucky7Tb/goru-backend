<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function applicationBank()
    {
        return $this->belongsTo(ApplicationBankAccount::class);
    }

    public function teacherPackage()
    {
        return $this->belongsTo(TeacherPackage::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class)->where('role', 'student');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class)->where('role', 'teacher');
    }
}
