<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\EligibleStudent; 
use App\Models\Attendance;
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
            'session' => 'required|string', 
            'password' => 'required|string|min:6',
            'passport_picture' => 'nullable|string', 
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
                'session' => $request->input('session'),
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

    public function loginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid login credentials.'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

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

        if ($user->role === 'student' && $user->studentProfile) {
            $responseData['user']['student_profile'] = [
                'student_id_number' => $user->studentProfile->student_id_number,
                'faculty' => $user->studentProfile->faculty,
                'department' => $user->studentProfile->department,
                'program' => $user->studentProfile->program,
                'level' => $user->studentProfile->level,
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
            'signature_image' => 'nullable|string', 
        ]);

        try {
            $imagePath = null;

            if ($request->filled('signature_image')) {
                $imageData = $request->signature_image;
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                }
                $imageData = base64_decode($imageData);
                
                $fileName = 'sig_' . time() . '_' . uniqid() . '.png';
                Storage::disk('public')->put('signatures/' . $fileName, $imageData);
                $imagePath = 'storage/signatures/' . $fileName;
            }

            User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'role' => 'invigilator',
                'signature_image' => $imagePath, 
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Invigilator account generated successfully with signature profile!'
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStudentVerifyProfile($student_id)
    {
        $profile = StudentProfile::where('student_id_number', $student_id)->first();

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access Denied: Student ID is not registered in the system.'
            ], 404);
        }

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

    // 👈 LOG ATTENDANCE METHOD (ONLY ONE CLEAN DEFINITION EXISTENT NOW)
    public function logStudentAttendance(Request $request)
    {
        $request->validate([
            'student_id_number' => 'required|string',
            'course_code' => 'required|string',
            'course_name' => 'required|string',
            'lecturer_name' => 'required|string|max:255',
            'hall' => 'required|string',
            'start_time' => 'required|date', 
            'end_time' => 'required|date|after:start_time',
            'invigilator_id' => 'required|integer',
            'paper_code' => 'required|string|max:50', 
        ]);

        $currentTime = now();

        $alreadyMarked = Attendance::where('student_id_number', $request->student_id_number)
            ->where('course_code', $request->course_code)
            ->where(function ($query) use ($currentTime) {
                $query->where('start_time', '<=', $currentTime)
                      ->where('end_time', '>=', $currentTime);
            })
            ->first();

        if ($alreadyMarked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Attendance rejected: This student has already logged into this specific active exam window room session!'
            ], 409);
        }

        try {
            Attendance::create([
                'student_id_number' => $request->student_id_number,
                'course_code' => $request->course_code,
                'course_name' => $request->course_name,
                'lecturer_name' => $request->lecturer_name, 
                'hall' => $request->hall,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'invigilator_id' => $request->invigilator_id,
                'paper_code' => trim($request->paper_code), 
                'verified_at' => $currentTime, 
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Student attendance logged successfully!'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database logging failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCourseSessionAnalytics($course_code)
    {
        $totalRegisteredCount = StudentProfile::count(); 
        $presentCount = Attendance::where('course_code', $course_code)->count();
        $absentCount = max(0, $totalRegisteredCount - $presentCount);
        $attendanceRate = $totalRegisteredCount > 0 ? round(($presentCount / $totalRegisteredCount) * 100, 1) : 0;

        return response()->json([
            'status' => 'success',
            'summary' => [
                'course_code' => $course_code,
                'total_allocated' => $totalRegisteredCount,
                'total_present' => $presentCount,
                'total_absent' => $absentCount,
                'attendance_rate_percentage' => $attendanceRate,
            ],
            'chart_dataset' => [
                ['label' => 'Present', 'value' => $presentCount],
                ['label' => 'Absent', 'value' => $absentCount],
            ]
        ], 200);
    }

    public function getCourseDetailedLedger($course_code)
    {
        $recordsLogged = Attendance::where('course_code', $course_code)->get();
        
        $firstLog = $recordsLogged->first();
        $invigilatorName = 'N/A';
        $signatureUrl = null;

        if ($firstLog) {
            $supervisor = User::find($firstLog->invigilator_id);
            if ($supervisor) {
                $invigilatorName = $supervisor->full_name;
                $signatureUrl = $supervisor->signature_image ? asset($supervisor->signature_image) : null;
            }
        }

        $studentIds = $recordsLogged->pluck('student_id_number')->toArray();
        $profilesMap = StudentProfile::whereIn('student_id_number', $studentIds)->get()->keyBy('student_id_number');

        $ledgerData = [];
        foreach ($recordsLogged as $log) {
            $profile = $profilesMap->get($log->student_id_number);
            
            $studentName = $profile && $profile->user ? $profile->user->full_name : 'Test Student';
            $sessionShift = $profile ? $profile->session : 'Morning';

            $ledgerData[] = [
                'index_number' => $log->student_id_number,
                'student_name' => $studentName,
                'session' => $sessionShift,
                'paper_code' => $log->paper_code ?? 'N/A',
                'time_logged' => $log->verified_at ? \Carbon\Carbon::parse($log->verified_at)->format('h:i:s A') : 'N/A',
                'status' => 'PRESENT',
            ];
        }

        usort($ledgerData, function ($a, $b) {
            return strcmp($a['student_name'], $b['student_name']);
        });

        return response()->json([
            'status' => 'success',
            'date_generated' => now()->format('l, F d, Y'),
            'total_records' => count($ledgerData),
            'invigilator_name' => $invigilatorName,
            'signature_picture' => $signatureUrl,
            'records' => $ledgerData
        ], 200);
    }

    public function logoutUser(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Token session terminated successfully.'
        ], 200);
    }
}