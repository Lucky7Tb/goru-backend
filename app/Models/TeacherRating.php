<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TeacherRating extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id')->where("role", "student");
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id')->where("role", "teacher");
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = Str::uuid();
            $model->created_at = now('Asia/Jakarta');
            $model->updated_at = now('Asia/Jakarta');
        });

        self::updating(function ($model) {
            $model->updated_at = now('Asia/Jakarta');
        });
    }

}
