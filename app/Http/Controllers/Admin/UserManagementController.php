<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Purok;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    // List users
    public function index(Request $request)
    {
        $query = User::with('purok');
        
        // Filter by purok if selected
        if ($request->filled('purok_id')) {
            $query->where('purok_id', $request->purok_id);
        }
        
        // Filter by role if needed
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $puroks = Purok::orderBy('name')->get();
        
        return view('admin.users.index', compact('users', 'puroks'));
    }
    
    // Show user profile
    public function show(User $user)
    {
        $user->load('purok');
        return view('admin.users.show', compact('user'));
    }

    // Show edit form for a user
    public function edit(User $user)
    {
        // List all possible roles from the database ENUM
        $roles = [
            'resident' => 'Resident',
            'purok_leader' => 'Purok Leader',
            'barangay_kagawad' => 'Barangay Kagawad',
            'barangay_captain' => 'Barangay Captain',
            'secretary' => 'Secretary',
            'sk_chairman' => 'SK Chairman',
            'admin' => 'Admin'
        ];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    // Update user role
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:resident,purok_leader,barangay_kagawad,barangay_captain,secretary,sk_chairman,admin',
            'is_approved' => 'nullable|boolean',
        ]);

        // Use DB query builder to ensure proper quoting
        \DB::table('users')
            ->where('id', $user->id)
            ->update([
                'role' => $validated['role'],
                'is_approved' => $validated['is_approved'] ?? 0,
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }
}
