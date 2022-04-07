<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ScheduleDetail extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'from_time' => 'datetime:H:i',
        'to_time' => 'datetime:H:i'
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

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
