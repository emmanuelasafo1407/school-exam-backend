<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function registerStudent(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string',
            'student_id_number' => 'required|string|unique:student_profiles',
            'faculty' => 'required|string',
            'department' => 'required|string',
            'program' => 'required|string',
            'level' => 'required|string',
            'password' => 'required|string|min:6',
            'passport_picture' => 'nullable|string', // Expecting base64 image string from Flutter
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'role' => 'student',
            ]);

            // Handle the passport photo storage if provided by the device
            $imagePath = null;
            if ($request->filled('passport_picture')) {
                $imageData = $request->passport_picture;
                // Strip metadata prefix if sent by Flutter image_picker
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                }
                $imageData = base64_decode($imageData);
                $fileName = 'passport_' . $request->student_id_number . '_' . time() . '.jpg';
                Storage::disk('public')->put('passports/' . $fileName, $imageData);
                $imagePath = 'storage/passports/' . $fileName;
            }

            StudentProfile::create([
                'user_id' => $user->id,
                'student_id_number' => $request->student_id_number,
                'faculty' => $request->faculty,
                'department' => $request->department,
                'program' => $request->program,
                'level' => $request->level,
                'passport_picture' => $imagePath,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Student registered successfully with passport photo!'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }
}