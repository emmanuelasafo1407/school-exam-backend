<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_code',
        'course_name',
        'exam_date',
        'start_time',
        'end_time',
        'venue',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'is_active' => 'boolean',
            // 'start_time' and 'end_time' are stored as time; 
            // no special cast needed unless you need specific formatting.
        ];
    }
}