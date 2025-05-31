<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    // List users
    public function index()
    {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }

    // Show edit form for a user
    public function edit(User $user)
    {
        // List possible roles here
        $roles = ['admin', 'purok_leader', 'barangay_official', 'resident'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    // Update user role
    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,purok_leader,barangay_official,resident',
            'is_approved' => 'required|boolean',
        ]);

        $user->role = $request->role;
        $user->is_approved = $request->is_approved;
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }
}
