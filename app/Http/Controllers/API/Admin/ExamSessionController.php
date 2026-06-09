<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use Illuminate\Http\Request;

class ExamSessionController extends Controller
{
    // List all sessions
    public function index()
    {
        return response()->json(ExamSession::all());
    }

    // Create a new exam session
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_code' => 'required|string',
            'course_name' => 'required|string',
            'exam_date'   => 'required|date',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'venue'       => 'required|string',
        ]);

        $session = ExamSession::create($validated);
        return response()->json($session, 201);
    }

    // Toggle active status (The "Gatekeeper" switch)
    public function toggleStatus(Request $request, $id)
    {
        $session = ExamSession::findOrFail($id);
        $session->is_active = $request->input('is_active');
        $session->save();

        return response()->json(['message' => 'Session updated', 'data' => $session]);
    }
}