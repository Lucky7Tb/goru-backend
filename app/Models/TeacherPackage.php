<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TeacherPackage extends Model
{
    use HasFactory;

    const PERDAY = "per_day";
    const PERWEEK = "per_week";
    const PERMONTH = "per_month";

    protected $keyType = 'string';

    protected $guarded = [
        "id"
    ];

    protected $casts = [
        "is_active" => "boolean"
    ];

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
