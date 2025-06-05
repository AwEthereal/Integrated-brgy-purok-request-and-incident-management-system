<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use App\Models\User;
use App\Models\Purok;
use Illuminate\Support\Facades\Auth;

class PurokLeaderController extends Controller
{
    // Show the purok leader dashboard
    public function dashboard()
    {
        $user = Auth::user()->load('purok');
        $purokId = $user->purok_id;
        $purokName = $user->purok ? $user->purok->name : 'Unknown Purok';

        // Get all clearance requests for this purok
        $requests = RequestModel::where('purok_id', $purokId)
            ->where('form_type', 'barangay_clearance')
            ->with('user')
            ->latest()
            ->get();
            
        // Get statistics
        $stats = [
            'total_requests' => $requests->count(),
            'pending_requests' => $requests->where('status', 'pending')->count(),
            'approved_requests' => $requests->where('status', 'approved')->count(),
            'rejected_requests' => $requests->where('status', 'rejected')->count(),
            'residents_count' => User::where('purok_id', $purokId)
                                  ->where('role', 'resident')
                                  ->count(),
        ];

        // Get residents for the dashboard (limited to 5)
        $residents = User::where('purok_id', $purokId)
            ->where('role', 'resident')
            ->withCount(['requests' => function($query) {
                $query->where('form_type', 'barangay_clearance');
            }])
            ->orderBy('last_name')
            ->take(5)
            ->get();

        return view('purok_leader.dashboard', compact('requests', 'stats', 'purokName', 'residents'));
    }
    
    /**
     * Show all residents in the purok leader's purok
     */
    public function residents()
    {
        $user = Auth::user();
        $purokName = $user->purok ? $user->purok->name : 'Unknown Purok';
        
        $residents = User::where('purok_id', $user->purok_id)
            ->where('role', 'resident')
            ->withCount(['requests' => function($query) {
                $query->where('form_type', 'barangay_clearance');
            }])
            ->latest()
            ->get();
            
        return view('purok_leader.residents', compact('residents', 'purokName'));
    }
}
