<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use App\Models\Request as ServiceRequest;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        
        $userId = $user->id;
        \Log::info('Starting dashboard for user ID: ' . $userId);
        
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
        
        // Log session data for debugging
        \Log::info('Session data: ' . json_encode($request->session()->all()));
        
        try {

            // Debug: Log user info
            \Log::info('Dashboard accessed by user:', [
                'user_id' => $userId,
                'email' => $user->email
            ]);

            // Get counts
            $pendingRequestsCount = \App\Models\Request::where('user_id', $userId)
                ->where('status', 'Pending')
                ->count();

            $incidentReportsCount = IncidentReport::where('user_id', $userId)->count();
            $resolvedIncidentsCount = IncidentReport::where('user_id', $userId)
                ->where('status', 'Resolved')
                ->count();
                
            $pendingIncidentsCount = IncidentReport::where('user_id', $userId)
                ->where('status', 'Pending')
                ->count();
                
            $completedRequestsCount = \App\Models\Request::where('user_id', $userId)
                ->whereIn('status', ['Completed', 'Rejected'])
                ->count();

            // Debug: Log counts
            \Log::info('Dashboard counts:', [
                'pending_requests' => $pendingRequestsCount,
                'total_incidents' => $incidentReportsCount,
                'resolved_incidents' => $resolvedIncidentsCount,
                'completed_requests' => $completedRequestsCount
            ]);

            // Get recent requests
            $recentRequests = \App\Models\Request::where('user_id', $userId)
                ->latest()
                ->limit(5)
                ->get(['id', 'purpose', 'status', 'created_at']);
                
            // Get completed requests
            $completedRequests = \App\Models\Request::where('user_id', $userId)
                ->whereIn('status', ['Completed', 'Rejected'])
                ->latest()
                ->limit(5)
                ->get(['id', 'purpose', 'status', 'created_at']);

            // Debug: Log recent requests
            \Log::info('Recent requests:', $recentRequests->toArray());

            // Format status labels for display
            $recentRequests->each(function($request) {
                $request->formatted_status = $this->formatStatus($request->status);
            });
            
            $completedRequests->each(function($request) {
                $request->formatted_status = $this->formatStatus($request->status);
            });
            
            // Get recent incident reports
            $recentIncidents = IncidentReport::where('user_id', $userId)
                ->latest()
                ->limit(5)
                ->get(['id', 'incident_type', 'status', 'created_at', 'description']);
                
            // Format incident statuses and add title from incident_type
            $recentIncidents->each(function($incident) {
                $incident->formatted_status = $this->formatStatus($incident->status);
                $incident->title = $incident->incident_type;
            });
            
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
                        'updated_at' => $request->updated_at
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
                    ->whereIn('status', ['Resolved', 'Rejected'])
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
                    $request->session()->flash('show_feedback_prompt', true);
                    $request->session()->flash('resolved_count', $resolvedCount);
                    $request->session()->put('recently_shown_feedback_prompt', true);
                }
            }

            // Debug: Log recent incidents
            \Log::info('Recent incidents:', $recentIncidents->toArray());

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
                'recentActivity' => $recentActivity,
                'showFeedbackPrompt' => $showFeedbackPrompt,
                'resolvedCount' => $resolvedCount,
                'user' => $user, // Pass the user object to the view
            ]);

        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Try to get user again in case of error
            $user = $user ?? $request->user();
            
            // Return a simple error response first
            if (!headers_sent()) {
                return response()->view('errors.500', [
                    'message' => 'An error occurred while loading the dashboard. Please try again later.',
                    'error' => $e->getMessage(),
                    'exception' => $e,
                    'user' => $user
                ], 500);
            } else {
                // If headers already sent, return a simple error message
                die('An error occurred while loading the dashboard. Please try again later.');
            }
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
