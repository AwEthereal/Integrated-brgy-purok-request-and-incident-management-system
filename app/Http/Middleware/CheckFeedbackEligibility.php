<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\IncidentReport;
use App\Models\Request as ServiceRequest;

class CheckFeedbackEligibility
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return $next($request);
        }

        // Don't show if already on feedback page
        if ($request->is('feedback*')) {
            return $next($request);
        }

        // Check if user has given feedback in the last month
        $recentFeedback = $user->feedback()
            ->where('created_at', '>=', now()->subMonth())
            ->exists();

        if ($recentFeedback) {
            return $next($request);
        }

        // Count resolved items that don't have feedback yet
        $resolvedIncidents = $user->incidentReports()
            ->whereIn('status', [\App\Models\IncidentReport::STATUS_RESOLVED, 'Rejected'])
            ->whereDoesntHave('feedback')
            ->count();

        $resolvedRequests = $user->requests()
            ->whereIn('status', ['Completed', 'Rejected'])
            ->whereDoesntHave('feedback')
            ->count();

        $totalResolved = $resolvedIncidents + $resolvedRequests;

        // If user has between 3-5 resolved items without feedback, show the feedback prompt
        if ($totalResolved >= 3 && $totalResolved <= 5) {
            // Check if user has skipped feedback recently
            if (!$request->cookie('skip_feedback')) {
                $request->session()->flash('show_feedback_prompt', true);
                $request->session()->flash('resolved_count', $totalResolved);
            }
        }

        return $next($request);
    }
}
