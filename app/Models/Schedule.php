<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->created_at = now("Asia/Jakarta");
            $model->updated_at = now("Asia/Jakarta");
        });

        self::updating(function ($model) {
            $model->updated_at = now("Asia/Jakarta");
        });
    }

    public function package()
    {
        return $this->belongsTo(TeacherPackage::class, 'teacher_package_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id')->where("role", "student");
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id')->where("role", "teacher");
    }

    public function transaction() 
    {
        return $this->hasOne(Transaction::class);
    }

    public function scheduleDetail()
    {
        return $this->hasMany(ScheduleDetail::class);
    }
}
