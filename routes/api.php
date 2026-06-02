<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

// Your live registration endpoint for the Infinix phone
Route::post('/register/student', [AuthController::class, 'registerStudent']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');