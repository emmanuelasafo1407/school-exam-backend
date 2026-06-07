<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
// Add your other models here (e.g., ExamSchedule)

class DashboardController extends Controller
{
    public function getStats() {
        return response()->json([
            'total_students' => User::where('role', 'student')->count(),
            'active_sessions' => 0, // Placeholder until you have an 'active' status
            'secure_bindings' => Attendance::count(), 
        ]);
    }
}