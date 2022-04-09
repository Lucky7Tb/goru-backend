<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherComment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function comment(){
        return $this->belongsTo(TeacherComment::where('teacher_id', auth()->user()->id)->get());
    }

    public function teacher()
    {
        return $this->belongsTo(User::class)->where('role', 'teacher');
    }
}
