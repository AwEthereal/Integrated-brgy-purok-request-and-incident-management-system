<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\User;
use App\Models\Request as ServiceRequest;
use App\Models\IncidentReport;
use App\Models\Purok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $rejectedRequests = ServiceRequest::where('status', 'rejected')->count();
        $allPendingRequests = ServiceRequest::where('status', 'pending')->count();
        
        // Get incident statistics
        $activeIncidents = IncidentReport::whereIn('status', ['pending', 'in_progress'])->count();
        $resolvedIncidents = IncidentReport::whereIn('status', ['resolved', 'approved'])->count();
        $pendingIncidents = IncidentReport::where('status', 'pending')->count();
        $inProgressIncidents = IncidentReport::where('status', 'in_progress')->count();
        
        // Get user statistics by role
        $residents = User::where('role', 'resident')->count();
        $approvedUsers = User::where('is_approved', true)->count();
        $pendingApproval = User::where('is_approved', false)->count();
        $purokLeaders = User::where('role', 'purok_leader')->count();
        $barangayOfficials = User::whereIn('role', ['barangay_captain', 'barangay_kagawad', 'secretary', 'sk_chairman'])->count();
        $admins = User::where('role', 'admin')->count();
        
        // Get purok statistics
        $puroks = Purok::withCount('users')->get();
        $purokData = $puroks->map(function($purok) {
            return [
                'name' => $purok->name,
                'count' => $purok->users_count
            ];
        });
        
        // Get recent users (last 5)
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        
        // Get recent requests (last 4)
        $recentRequests = ServiceRequest::with(['user', 'purok'])
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();
        
        // Monthly trends (last 6 months)
        $monthlyRequests = [];
        $monthlyIncidents = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyRequests[] = [
                'month' => $month->format('M'),
                'count' => ServiceRequest::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count()
            ];
            $monthlyIncidents[] = [
                'month' => $month->format('M'),
                'count' => IncidentReport::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count()
            ];
        }
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalPuroks',
            'pendingRequests',
            'completedRequests',
            'rejectedRequests',
            'allPendingRequests',
            'activeIncidents',
            'resolvedIncidents',
            'pendingIncidents',
            'inProgressIncidents',
            'residents',
            'approvedUsers',
            'pendingApproval',
            'purokLeaders',
            'barangayOfficials',
            'admins',
            'recentUsers',
            'recentRequests',
            'purokData',
            'monthlyRequests',
            'monthlyIncidents'
        ));
    }

    public function purgeData(Request $request)
    {
        $request->validate([
            'confirm' => ['required', 'string'],
        ]);

        if (trim((string) $request->input('confirm')) !== 'DELETE ALL DATA') {
            return back()->with('error', 'Confirmation phrase mismatch. Type "DELETE ALL DATA" to proceed.');
        }

        try {
            DB::transaction(function () {
                Feedback::query()->update([
                    'request_id' => null,
                    'incident_report_id' => null,
                ]);

                ServiceRequest::query()->delete();
                IncidentReport::query()->delete();
            });

            return back()->with('success', 'Purge completed. All requests and incident reports were deleted.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Purge failed. Please try again.');
        }
    }
}
