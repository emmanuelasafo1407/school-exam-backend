<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentImportController extends Controller
{
    public function importCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
        
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // Skip header row

        $count = 0;
        while (($data = fgetcsv($handle)) !== FALSE) {
            // Mapping CSV columns: 0: name, 1: email, 2: student_id, 3: level, 4: program
            User::updateOrCreate(
                ['email' => $data[1]],
                [
                    'full_name' => $data[0],
                    'password' => Hash::make('password123'), // Default password
                    'role' => 'student',
                ]
            );
            // Logic to link student_profile table goes here
            $count++;
        }
        fclose($handle);

        return response()->json(['message' => "$count students imported successfully"]);
    }
}