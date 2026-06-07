<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExamSchedule;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function getDashboardSummary(Request $request)
    {
        $user = Auth::user();
        
        // 1. Get the authenticated student's profile
        $profile = StudentProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student profile not found.'
            ], 404);
        }

        // 2. Fetch all upcoming exam papers for this student's program and level
        // We select the specific columns to include the new location details
        $upcomingExams = ExamSchedule::where('program', $profile->program)
            ->where('level', $profile->level)
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->get([
                'course_code', 
                'course_name', 
                'start_time', 
                'end_time', 
                'hall', 
                'lecturer_name', 
                'floor_level', 
                'map_link', 
                'seating_info'
            ]);

        // 3. Format the response
        return response()->json([
            'status' => 'success',
            'student_info' => [
                'name' => $user->full_name,
                'index_number' => $profile->student_id_number,
                'program' => $profile->program,
                'level' => $profile->level,
                'session' => $profile->session,
                'passport_picture' => $profile->passport_picture 
                    ? asset('storage/' . $profile->passport_picture) 
                    : null,
            ],
            'exams' => $upcomingExams
        ], 200);
    }
}