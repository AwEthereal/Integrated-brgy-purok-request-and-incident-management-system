<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Summary stats
        $pendingRequestsCount = \App\Models\Request::where('user_id', $userId)
            ->where('status', 'Pending')
            ->count();

        $incidentReportsCount = \App\Models\IncidentReport::where('user_id', $userId)->count();

        $resolvedIncidentsCount = \App\Models\IncidentReport::where('user_id', $userId)
            ->where('status', 'Resolved')
            ->count();

        // Recent activity (merge requests and incident reports)
        $requests = \App\Models\Request::selectRaw("id, purpose AS description, status, created_at, 'Request' as type")
            ->where('user_id', auth()->id());

        $incidentReports = \App\Models\IncidentReport::selectRaw("id, description, status, created_at, 'Incident' as type")
            ->where('user_id', auth()->id());

        $recentActivities = $requests->unionAll($incidentReports)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();


        // Notifications (basic example, customize as needed)
        $notifications = [
            'Your last incident report status changed to Resolved.',
            'New clearance request feature is now available.',
        ];

        return view('dashboard', compact(
            'pendingRequestsCount',
            'incidentReportsCount',
            'resolvedIncidentsCount',
            'recentActivities',
            'notifications'
        ));
    }
}
