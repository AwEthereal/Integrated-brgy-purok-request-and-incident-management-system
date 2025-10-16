<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Request as ServiceRequest;
use App\Models\IncidentReport;
use App\Models\Purok;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get total counts
        $totalUsers = User::count();
        $totalPuroks = Purok::count();
        
        // Get request statistics
        $pendingRequests = ServiceRequest::where('status', 'purok_approved')->count();
        $completedRequests = ServiceRequest::whereIn('status', ['completed', 'barangay_approved'])->count();
        
        // Get incident statistics
        $activeIncidents = IncidentReport::whereIn('status', ['pending', 'in_progress'])->count();
        $resolvedIncidents = IncidentReport::whereIn('status', ['resolved', 'approved'])->count();
        
        // Get user statistics
        $approvedUsers = User::where('is_approved', true)->count();
        $pendingApproval = User::where('is_approved', false)->count();
        $purokLeaders = User::whereIn('role', ['purok_leader', 'purok_president'])->count();
        $barangayOfficials = User::whereIn('role', ['barangay_captain', 'barangay_kagawad', 'secretary', 'sk_chairman'])->count();
        
        // Get recent users (last 5)
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        
        // Get recent requests (last 5)
        $recentRequests = ServiceRequest::with(['user', 'purok'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalPuroks',
            'pendingRequests',
            'completedRequests',
            'activeIncidents',
            'resolvedIncidents',
            'approvedUsers',
            'pendingApproval',
            'purokLeaders',
            'barangayOfficials',
            'recentUsers',
            'recentRequests'
        ));
    }
}
