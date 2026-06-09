<?php

namespace Database\Seeders;

use App\Models\Invigilator;
use Illuminate\Database\Seeder;

class InvigilatorSeeder extends Seeder
{
    public function run(): void
    {
        Invigilator::create([
            'user_id' => 1, // Make sure a user with ID 1 exists in your users table
            'staff_id' => 'STAFF-001',
            'is_verified' => false,
        ]);

        Invigilator::create([
            'user_id' => 2, // Make sure a user with ID 2 exists
            'staff_id' => 'STAFF-002',
            'is_verified' => false,
        ]);
    }
}