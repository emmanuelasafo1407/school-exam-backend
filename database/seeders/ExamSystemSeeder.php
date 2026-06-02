<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EligibleStudent;
use App\Models\ExamSchedule;

class ExamSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed the Admin's master list of eligible students
        EligibleStudent::create([
            'student_id_number' => '1706547871',
            'full_name' => 'Emmanuel Asafo',
            'level' => '300',
            'semester' => 'Sem 2 2026',
            'has_registered' => false,
        ]);

        EligibleStudent::create([
            'student_id_number' => '1706547899',
            'full_name' => 'John Doe',
            'level' => '300',
            'semester' => 'Sem 2 2026',
            'has_registered' => false,
        ]);

        // 2. Seed some exam timetable schedules for Level 300 Computer Engineering
        ExamSchedule::create([
            'course_name' => 'Computer Architecture',
            'course_code' => 'BCE 302',
            'hall' => 'Engineering Block Rm 4',
            'exam_date' => '2026-06-15',
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
            'level' => '300',
        ]);

        ExamSchedule::create([
            'course_name' => 'Embedded Systems Engineering',
            'course_code' => 'BCE 304',
            'hall' => 'Main Auditorium Hall B',
            'exam_date' => '2026-06-18',
            'start_time' => '13:30:00',
            'end_time' => '16:30:00',
            'level' => '300',
        ]);
    }
}