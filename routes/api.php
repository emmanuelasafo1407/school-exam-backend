<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

// Your live registration endpoint for the Infinix phone
Route::post('/register/student', [AuthController::class, 'registerStudent']);
Route::post('/login', [AuthController::class, 'loginUser']);
Route::post('/register/invigilator', [AuthController::class, 'registerInvigilator']);
Route::get('/student/verify/{student_id}', [AuthController::class, 'getStudentVerifyProfile']);
Route::post('/attendance/log', [AuthController::class, 'logStudentAttendance']);
Route::get('/attendance/analytics/{course_code}', [AuthController::class, 'getCourseSessionAnalytics']);
Route::get('/attendance/ledger/{course_code}', [AuthController::class, 'getCourseDetailedLedger']);
// Ensure this path matches exactly what the ApiClient is calling:
Route::get('/student-profile/{student_id}', [App\Http\Controllers\API\AuthController::class, 'getStudentVerifyProfile']);
// 👈 FIX: Ensure the route uri is exactly 'log-attendance' to match your ApiClient
Route::post('/log-attendance', [App\Http\Controllers\API\AuthController::class, 'logStudentAttendance']);
Route::post('/submit-paper', [App\Http\Controllers\API\AuthController::class, 'submitExamPaper']);
Route::get('/admin/stats', [App\Http\Controllers\API\DashboardController::class, 'getStats']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function () {
    // Secure token destruction lane
    Route::post('/logout', [AuthController::class, 'logoutUser']);
    
    // Your existing active attendance and ledger routes sit down here...
});