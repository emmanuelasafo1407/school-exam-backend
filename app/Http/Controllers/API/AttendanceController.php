<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\ExamSession;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function logScan(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'exam_session_id' => 'required|exists:exam_sessions,id',
        ]);

        // Verify session is active before allowing the log
        $session = ExamSession::findOrFail($request->exam_session_id);
        if (!$session->is_active) {
            return response()->json(['message' => 'Session is not active'], 403);
        }

        $log = AttendanceLog::create([
            'student_id' => $request->student_id,
            'exam_session_id' => $request->exam_session_id,
            'invigilator_id' => auth()->id(),
            'hall' => $request->hall,
        ]);

        return response()->json(['message' => 'Attendance logged', 'data' => $log], 201);
    }
    public function index()
{
    // Fetches logs with student and session details
    return AttendanceLog::with(['student', 'examSession'])->latest()->get();
}
}