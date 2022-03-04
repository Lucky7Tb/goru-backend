<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeacherLevel extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'level_id' => 'string',
        'user_id' => 'string'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = Str::uuid();
            $model->created_at = now("Asia/Jakarta");
            $model->updated_at = now("Asia/Jakarta");
        });

        self::updating(function ($model) {
            $model->updated_at = now("Asia/Jakarta");
        });
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
