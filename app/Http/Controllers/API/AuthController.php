<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\EligibleStudent; // 👈 Imported for checking eligibility
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
            'passport_picture' => 'nullable|string', 
        ]);

        // 1. Guard Check: Is this student ID registered on the Admin's master eligibility list?
        $eligible = EligibleStudent::where('student_id_number', $request->student_id_number)->first();

        if (!$eligible) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration blocked: Your student ID is not listed on the official semester eligibility list. Contact Admin.'
            ], 403);
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'role' => 'student',
            ]);

            $imagePath = null;
            if ($request->filled('passport_picture')) {
                $imageData = $request->passport_picture;
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

            // 2. Update the master eligibility list flag to track that onboarding is complete
            $eligible->update(['has_registered' => true]);

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

    // 👈 NEW: Secure Multi-Role Login Endpoint
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Verify user and match hashed password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid login credentials.'
            ], 401);
        }

        // Generate dynamic access token using Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // Build base payload response
        $responseData = [
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ];

        // Fetch related profile details if the logging account is a student
        if ($user->role === 'student' && $user->studentProfile) {
            $responseData['user']['student_profile'] = [
                'student_id_number' => $user->studentProfile->student_id_number,
                'faculty' => $user->studentProfile->faculty,
                'department' => $user->studentProfile->department,
                'program' => $user->studentProfile->program,
                'level' => $user->studentProfile->level,
                // 👈 UPDATED: Generate a clean, bulletproof public asset link
                'passport_picture' => $user->studentProfile->passport_picture ? asset($user->studentProfile->passport_picture) : null,
            ];
        }

        return response()->json($responseData, 200);
    }
    public function registerInvigilator(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string',
            'password' => 'required|string|min:6',
        ]);

        try {
            User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'role' => 'invigilator', // Explicitly marked as supervisor tier
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Invigilator account generated successfully!'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }
    // 👈 NEW: Fetch a student's verification profile using their scanned ID number
    public function getStudentVerifyProfile($student_id)
    {
        $profile = StudentProfile::where('student_id_number', $student_id)->first();

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access Denied: Student ID is not registered in the system.'
            ], 404);
        }

        // Fetch user account details associated with this profile record
        $user = $profile->user;

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $user->full_name,
                'student_id_number' => $profile->student_id_number,
                'faculty' => $profile->faculty,
                'department' => $profile->department,
                'program' => $profile->program,
                'level' => $profile->level,
                'passport_picture' => $profile->passport_picture ? asset($profile->passport_picture) : null,
            ]
        ], 200);
    }
}