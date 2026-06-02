<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

// Your live registration endpoint for the Infinix phone
Route::post('/register/student', [AuthController::class, 'registerStudent']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register/invigilator', [AuthController::class, 'registerInvigilator']);
Route::get('/student/verify/{student_id}', [AuthController::class, 'getStudentVerifyProfile']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');