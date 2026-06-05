<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create default test account using the correct 'full_name' column attributes safely
        User::factory()->create([
            'full_name' => 'Test Admin/Invigilator',
            'email' => 'test@example.com',
            'role' => 'invigilator',
        ]);

        // 2. 👈 Trigger your custom student eligibility checklist dataset loader live!
        $this->call([
            EligibleStudentsSeeder::class,
        ]);
    }
}