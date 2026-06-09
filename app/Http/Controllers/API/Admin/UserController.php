<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get a list of all users, filtered by role.
     * Useful for your admin "Class Members" or "User Management" page.
     */
    public function index(Request $request)
    {
        $role = $request->query('role'); // e.g., 'student' or 'invigilator'
        
        $query = User::query();

        if ($role) {
            $query->where('role', $role);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get()
        ]);
    }

    /**
     * Update user status (Qualification for students, Verification for invigilators).
     */
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate the incoming status update
        $request->validate([
            'is_qualified' => 'boolean',
            'is_verified'  => 'boolean',
            'assigned_hall' => 'nullable|string',
        ]);

        $user->update($request->only(['is_qualified', 'is_verified', 'assigned_hall']));

        return response()->json([
            'message' => 'Status updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Delete a user if necessary.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}