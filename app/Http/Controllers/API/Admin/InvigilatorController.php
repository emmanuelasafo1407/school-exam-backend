<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invigilator;
use Illuminate\Http\Request;

class InvigilatorController extends Controller
{
    // Get list of unverified staff for the admin to review
    public function index()
    {
        return Invigilator::where('is_verified', false)
            ->with('user:id,full_name,email') 
            ->get();
    }

    // Admin approves the invigilator
    public function verify($id)
    {
        $invigilator = Invigilator::findOrFail($id);
        $invigilator->update(['is_verified' => true]);
        
        return response()->json(['message' => 'Invigilator verified successfully']);
    }
}