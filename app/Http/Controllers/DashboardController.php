<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use App\Models\Purok;
use App\Models\Request as ServiceRequest;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(HttpRequest $request)
    {
        // Get user first, before any database operations
        $user = $request->user();
        if (!$user) {
            \Log::error('No authenticated user found in dashboard');
            return redirect()->route('login');
        }

        // Check if user has required role
        $allowedRoles = ['barangay_captain', 'barangay_kagawad', 'secretary', 'sk_chairman', 'admin', 'resident'];
        if (!in_array($user->role, $allowedRoles)) {
            \Log::warning('Unauthorized access attempt to dashboard', [
                'user_id' => $user->id,
                'role' => $user->role,
                'allowed_roles' => $allowedRoles
            ]);
            
            if ($user->role === 'purok_leader' || $user->role === 'purok_president') {
                return redirect()->route('purok_leader.dashboard');
            }
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }

        // Redirect admin to admin dashboard
        if ($user->role === 'admin') {
            return app(\App\Http\Controllers\Admin\AdminDashboardController::class)->index();
        }

        // Redirect barangay officials to their dedicated dashboard
        if (in_array($user->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'sk_chairman'])) {
            // Get all puroks
            $puroks = Purok::orderBy('name')->get();
            $selectedPurok = $request->query('purok');

            // Get active incidents (pending or in_progress)
            $incidentsQuery = IncidentReport::with(['user', 'purok'])
                ->whereIn('status', ['pending', 'in_progress']);
                
            if ($selectedPurok) {
                $incidentsQuery->where('purok_id', $selectedPurok);
            }
            
            $incidents = $incidentsQuery->orderByRaw(
                "CASE 
                    WHEN status = 'pending' THEN 1 
                    WHEN status = 'in_progress' THEN 2 
                    ELSE 3 
                END"
            )->orderBy('created_at', 'desc')
            ->get();
            
            // Get pending service requests (purok_approved only)
            $pendingRequestsQuery = ServiceRequest::with(['user', 'purok'])
                ->where('status', 'purok_approved');
            
            if ($selectedPurok) {
                $pendingRequestsQuery->where('purok_id', $selectedPurok);
            }
            
            $pendingRequests = $pendingRequestsQuery->orderBy('created_at', 'desc')->get();
            
            return view('barangay_official.dashboard', [
                'pendingRequests' => $pendingRequests,
                'incidents' => $incidents,
                'puroks' => $puroks,
                'selectedPurok' => $selectedPurok
            ]);
        }
        
        $userId = $user->id;
        
        // Initialize variables with default values
        $pendingRequestsCount = 0;
        $incidentReportsCount = 0;
        $resolvedIncidentsCount = 0;
        $pendingIncidentsCount = 0;
        $completedRequestsCount = 0;
        $recentRequests = collect();
        $completedRequests = collect();
        $recentIncidents = collect();
        $recentActivity = collect();
        $showFeedbackPrompt = false;
        $resolvedCount = 0;
        
        
        try {

            // Get counts
            $pendingRequestsCount = ServiceRequest::where('user_id', $userId)
                ->where('status', 'Pending')
                ->count();

            $incidentReportsCount = IncidentReport::where('user_id', $userId)->count();
            $resolvedIncidentsCount = IncidentReport::where('user_id', $userId)
                ->where('status', IncidentReport::STATUS_RESOLVED)
                ->count();
                
            $pendingIncidentsCount = IncidentReport::where('user_id', $userId)
                ->where('status', 'Pending')
                ->count();
                
            $completedRequestsCount = ServiceRequest::where('user_id', $userId)
                ->where('status', 'barangay_approved')
                ->count();


            // Get recent requests
            $recentRequests = ServiceRequest::with(['user', 'purok'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(['id', 'purpose', 'status', 'created_at', 'updated_at', 'last_viewed_at']);
                
            // Get completed requests
            $completedRequests = ServiceRequest::where('user_id', $userId)
                ->whereIn('status', ['Completed', 'Rejected'])
                ->latest()
                ->limit(5)
                ->get(['id', 'purpose', 'status', 'created_at']);


            // Format status labels for display
            $recentRequests->each(function($request) {
                $request->formatted_status = $this->formatStatus($request->status);
            });
            
            $completedRequests->each(function($request) {
                $request->formatted_status = $this->formatStatus($request->status);
            });
            
            // Get pending reports (both requests and incidents)
            $pendingReports = collect();
            
            // Get pending service requests
            $pendingServiceRequests = ServiceRequest::where('user_id', $userId)
                ->where('status', 'pending')
                ->with('purok')
                ->latest()
                ->limit(5)
                ->get(['id', 'purpose as description', 'status', 'created_at', 'purok_id']);
                
            // Add type and format status for service requests
            $pendingServiceRequests->each(function($request) {
                $request->type = 'request';
                $request->formatted_status = $this->formatStatus($request->status);
                $request->title = 'Request: ' . $request->description;
            });
            
            // Get pending incident reports
            $pendingIncidents = IncidentReport::where('user_id', $userId)
                ->where('status', 'pending')
                ->with('purok')
                ->latest()
                ->limit(5)
                ->get(['id', 'incident_type', 'status', 'created_at', 'description', 'purok_id']);
                
            // Add type and format status for incidents
            $pendingIncidents->each(function($incident) {
                $incident->type = 'incident';
                $incident->formatted_status = $this->formatStatus($incident->status);
                $incident->title = 'Incident: ' . ($incident->incident_type ?? 'Report');
            });
            
            // Combine and sort all pending reports by creation date
            $pendingReports = $pendingServiceRequests->merge($pendingIncidents)
                ->sortByDesc('created_at')
                ->take(5);
                
            // For backward compatibility
            $recentIncidents = collect();
            $pendingIncidents = collect();
            
            // Get recent activity (combined requests and incidents)
            $recentActivity = collect()
                ->merge($recentRequests->map(function($request) {
                    return (object)[
                        'type' => 'request',
                        'id' => $request->id,
                        'title' => 'Request: ' . $request->purpose,
                        'status' => $request->status,
                        'formatted_status' => $request->formatted_status,
                        'created_at' => $request->created_at,
                        'updated_at' => $request->updated_at,
                        'last_viewed_at' => $request->last_viewed_at ?? null
                    ];
                }))
                ->merge($recentIncidents->map(function($incident) {
                    return (object)[
                        'type' => 'incident',
                        'id' => $incident->id,
                        'title' => 'Incident: ' . $incident->title,
                        'status' => $incident->status,
                        'formatted_status' => $incident->formatted_status,
                        'created_at' => $incident->created_at,
                        'updated_at' => $incident->updated_at
                    ];
                }))
                ->sortByDesc('created_at')
                ->take(5);

            // Check if user should see feedback prompt
            $showFeedbackPrompt = false;
            $resolvedCount = 0;
            
            if (!$request->session()->has('recently_shown_feedback_prompt')) {
                // Count resolved items without feedback
                $resolvedIncidents = $user->incidentReports()
                    ->whereIn('status', [IncidentReport::STATUS_RESOLVED, 'Rejected'])
                    ->whereDoesntHave('feedback')
                    ->count();

                $resolvedRequests = $user->requests()
                    ->whereIn('status', ['Completed', 'Rejected'])
                    ->whereDoesntHave('feedback')
                    ->count();

                $totalResolved = $resolvedIncidents + $resolvedRequests;
                $showFeedbackPrompt = $totalResolved >= 3 && $totalResolved <= 5 && !$request->cookie('skip_feedback');
                $resolvedCount = $totalResolved;
                
                if ($showFeedbackPrompt) {
                    session([
                        'show_feedback_prompt' => true,
                        'resolved_count' => $resolvedCount,
                        'recently_shown_feedback_prompt' => true
                    ]);
                }
            }


            // Prepare recent activities - use the existing collections
            $recentActivities = collect()
                ->merge($recentRequests->map(function($request) {
                    return (object)[
                        'id' => $request->id,
                        'description' => $request->purpose,
                        'status' => $request->status,
                        'created_at' => $request->created_at,
                        'formatted_date' => $request->created_at->toIso8601String(),
                        'type' => 'Request',
                        'url' => route('requests.show', $request->id)
                    ];
                }))
                ->merge($recentIncidents->map(function($incident) {
                    return (object)[
                        'id' => $incident->id,
                        'description' => $incident->title, // Using title instead of description for consistency
                        'status' => $incident->status,
                        'created_at' => $incident->created_at,
                        'formatted_date' => $incident->created_at->toIso8601String(),
                        'type' => 'Incident',
                        'incident_type' => $incident->incident_type ?? 'General',
                        'url' => route('incident_reports.show', $incident->id)
                    ];
                }));

            // Sort by created_at desc and take 5 most recent
            $recentActivities = $recentActivities->sortByDesc('created_at')->take(5);

            // Return the dashboard view with the data
            return view('dashboard', [
                'pendingRequestsCount' => $pendingRequestsCount,
                'incidentReportsCount' => $incidentReportsCount,
                'resolvedIncidentsCount' => $resolvedIncidentsCount,
                'pendingIncidentsCount' => $pendingIncidentsCount,
                'completedRequestsCount' => $completedRequestsCount,
                'recentRequests' => $recentRequests,
                'completedRequests' => $completedRequests,
                'recentIncidents' => $recentIncidents,
                'pendingIncidents' => $pendingIncidents,
                'pendingReports' => $pendingReports, // Add this line
                'recentActivity' => $recentActivity,
                'showFeedbackPrompt' => $showFeedbackPrompt,
                'resolvedCount' => $resolvedCount
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error loading dashboard data: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'exception' => $e
            ]);
            
            // Return to dashboard with error message
            return redirect()->route('dashboard')
                ->with('error', 'An error occurred while loading dashboard data. Please try again.');
        }
    }
    
    /**
     * Format status by replacing underscores with spaces and capitalizing words
     */
    private function formatStatus($status)
    {
        if (empty($status)) {
            return '';
        }
        
        // Replace underscores with spaces and capitalize each word
        return ucwords(str_replace('_', ' ', $status));
    }
}
