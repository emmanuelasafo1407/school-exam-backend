<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Ensure this is imported
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'full_name' => 'System Admin',
            'email' => 'admin@school.com',
            'password' => Hash::make('password123'), // Securely hashes the password
            'role' => 'admin',
        ]);
    }
}