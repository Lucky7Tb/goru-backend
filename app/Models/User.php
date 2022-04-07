<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $keyType = 'string';

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_ban' => 'boolean',
        'is_recommended' => 'boolean',
    ];

    public function role(): Attribute
    {
        return new Attribute(
            get: fn ($value) => ucfirst($value)
        );
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = Str::uuid();
            $model->created_at = now("Asia/Jakarta");
            $model->updated_at = now("Asia/Jakarta");
        });

        self::updating(function($model) {
            $model->updated_at = now("Asia/Jakarta");
        });
    }

    public function scopeIsAdmin($query)
    {
        return $query->where('role', '=', 'admin');
    }

    public function scopeIsStudent($query)
    {
        return $query->where('role', '=', 'student');

    }

    public function scopeIsTeacher($query)
    {
        return $query->where('role', '=', 'teacher');
    }

    public function ratings()
    {
        return $this->hasMany(TeacherRating::class);
    }

    public function package()
    {
        return $this->hasMany(TeacherPackage::class);
    }

    public function teacherLessonSubject()
    {
        return $this->hasMany(TeacherLessonSubject::class);
    }
}
