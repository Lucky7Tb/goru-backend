<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class TeacherRequestWallet extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function bankAccountNumber(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Crypt::decryptString($value),
        );
    }

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

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
