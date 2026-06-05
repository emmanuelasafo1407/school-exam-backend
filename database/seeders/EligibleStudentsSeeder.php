<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EligibleStudent;

class EligibleStudentsSeeder extends Seeder
{
    public function run(): void
    {
        EligibleStudent::create([
            'student_id_number' => '1705378674',
            'student_name' => 'Junior Makafui',
            'level' => '100',           // 👈 ADDED: Matches migration field requirement
            'semester' => 'Sem 1 2026', // 👈 ADDED: Matches migration field requirement
            'has_registered' => false,
        ]);
    }
}