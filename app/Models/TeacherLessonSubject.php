<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TeacherLessonSubject extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    protected $guarded = ['id'];

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

    public function lessonSubject()
    {
        return $this->belongsTo(LessonSubject::class);
    }
}
