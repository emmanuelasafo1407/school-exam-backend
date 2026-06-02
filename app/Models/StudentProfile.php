<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id_number',
        'faculty',
        'department',
        'program',
        'level',
        'passport_picture',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}