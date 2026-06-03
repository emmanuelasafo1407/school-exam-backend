<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    /**
     * The attributes that are mass assignable.
     * * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'student_id_number',
        'faculty',
        'department',
        'program',
        'level',
        'session', // 👈 FIXED: Explicit assignment clearance allows injection context bypassing
        'passport_picture',
    ];

    /**
     * Relational connection link mapping back to user record account.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}