<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EligibleStudent extends Model
{
    protected $fillable = [
        'student_id_number',
        'student_name',
        'level',    // 👈 ENSURE THIS LINE IS HERE
        'semester', // 👈 ENSURE THIS LINE IS HERE
        'has_registered',
    ];
}