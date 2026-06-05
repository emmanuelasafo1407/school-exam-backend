<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    // 👈 FIXED: Explicitly allow these keys to be passed into the Attendance::create() method
    protected $fillable = [
        'student_id_number',
        'course_code',
        'course_name',
        'hall',
        'lecturer_name', 
        'start_time',
        'end_time',
        'paper_code',
        'invigilator_id',
        'verified_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'paper_code' => 'string',
    ];
}