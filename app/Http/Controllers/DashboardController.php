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
        try {
            $user = $request->user();
            $userId = $user->id;

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

            // Get recent incidents
            $recentIncidents = IncidentReport::where('user_id', $userId)
                ->latest()
                ->limit(5)
                ->get(['id', 'incident_type', 'description', 'status', 'created_at']);

            // Debug: Log recent incidents
            \Log::info('Recent incidents:', $recentIncidents->toArray());

            // Prepare recent activities
            $recentActivities = collect();
            
            // Add requests to activities
            foreach ($recentRequests as $request) {
                $recentActivities->push((object)[
                    'id' => $request->id,
                    'description' => $request->purpose,
                    'status' => $request->status,
                    'created_at' => $request->created_at,
                    'formatted_date' => $request->created_at->toIso8601String(),
                    'type' => 'Request',
                    'url' => route('requests.show', $request->id)
                ]);
            }
            
            // Add incidents to activities
            foreach ($recentIncidents as $incident) {
                $recentActivities->push((object)[
                    'id' => $incident->id,
                    'description' => $incident->description,
                    'status' => $incident->status,
                    'created_at' => $incident->created_at,
                    'formatted_date' => $incident->created_at->toIso8601String(),
                    'type' => 'Incident',
                    'incident_type' => $incident->incident_type,
                    'url' => route('incident_reports.show', $incident->id)
                ]);
            }

            // Sort by created_at desc and take 5 most recent
            $recentActivities = $recentActivities->sortByDesc('created_at')->take(5);

            return view('dashboard', [
                'pendingRequestsCount' => $pendingRequestsCount,
                'incidentReportsCount' => $incidentReportsCount,
                'resolvedIncidentsCount' => $resolvedIncidentsCount,
                'pendingIncidentsCount' => $pendingIncidentsCount,
                'completedRequestsCount' => $completedRequestsCount,
                'recentRequests' => $recentRequests,
                'completedRequests' => $completedRequests,
                'recentIncidents' => $recentIncidents,
                'recentActivities' => $recentActivities,
            ]);

        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage());
            return view('dashboard')->with('error', 'An error occurred while loading the dashboard.');
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
