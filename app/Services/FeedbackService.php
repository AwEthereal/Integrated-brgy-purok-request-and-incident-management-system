<?php

namespace App\Services;

use App\Models\Request as ServiceRequest;
use App\Models\IncidentReport;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class FeedbackService
{
    /**
     * Check if user has pending feedback for any resolved/approved items
     * 
     * @return array|null Array with type and id of item needing feedback, or null if none
     */
    public function getPendingFeedbackItem()
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }
        
        // Check if we already checked for this user in this session
        if (session()->has('feedback_checked')) {
            return session('pending_feedback');
        }
        
        // Mark that we've checked for feedback in this session
        session(['feedback_checked' => true]);
        
        // Check for pending request feedback
        $pendingRequest = ServiceRequest::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'completed'])
            ->whereNull('feedback_provided_at')
            ->where('feedback_skipped', false)
            ->whereNull('feedback_dismissed_at')
            ->orderBy('updated_at', 'desc')
            ->first();
            
        if ($pendingRequest) {
            $feedbackData = [
                'type' => 'request',
                'id' => $pendingRequest->id,
                'title' => 'Request: ' . ($pendingRequest->form_type ?? 'Service Request'),
                'item' => $pendingRequest->toArray()
            ];
            
            // Mark that we've requested feedback for this item
            $pendingRequest->update(['feedback_requested_at' => now()]);
            
            session(['pending_feedback' => $feedbackData]);
            return $feedbackData;
        }
        
        // Check for pending incident report feedback
        $pendingIncident = IncidentReport::where('user_id', $user->id)
            ->where('status', 'Resolved')
            ->where('feedback_skipped', false)
            ->whereNull('feedback_submitted_at')
            ->whereNull('feedback_dismissed_at')
            ->orderBy('resolved_at', 'desc')
            ->first();
            
        if ($pendingIncident) {
            $feedbackData = [
                'type' => 'incident',
                'id' => $pendingIncident->id,
                'title' => 'Incident: ' . ($pendingIncident->incident_type ?? 'Incident Report'),
                'item' => $pendingIncident->toArray()
            ];
            
            // Mark that we've requested feedback for this item
            $pendingIncident->update(['feedback_requested_at' => now()]);
            
            session(['pending_feedback' => $feedbackData]);
            return $feedbackData;
        }
        
        // Clear any existing pending feedback in the session
        session()->forget('pending_feedback');
        return null;
    }
    
    /**
     * Mark a request as requiring feedback
     */
    public function requestFeedbackForRequest(ServiceRequest $request)
    {
        // Only request feedback if it hasn't been requested already
        if (!$request->feedback_requested_at) {
            $request->update([
                'feedback_requested_at' => now(),
                'feedback_dismissed_at' => null,
                'feedback_skipped' => false
            ]);
        }
    }
    
    /**
     * Mark an incident as requiring feedback
     */
    public function requestFeedbackForIncident(IncidentReport $incident)
    {
        // Only request feedback if it hasn't been requested already
        if (!$incident->feedback_requested_at) {
            $incident->update([
                'feedback_requested_at' => now(),
                'feedback_dismissed_at' => null,
                'feedback_skipped' => false
            ]);
        }
    }
    
    /**
     * Mark feedback as skipped for a request
     * @param ServiceRequest|Collection $request The request model or collection
     */
    public function skipFeedbackForRequest($request)
    {
        if ($request instanceof Collection) {
            $request = $request->first();
        }
        
        $request->update([
            'feedback_skipped' => true,
            'feedback_dismissed_at' => now(),
        ]);
    }
    
    /**
     * Mark feedback as skipped for an incident report
     * @param IncidentReport|Collection $incident The incident model or collection
     */
    public function skipFeedbackForIncident($incident)
    {
        if ($incident instanceof Collection) {
            $incident = $incident->first();
        }
        
        $incident->update([
            'feedback_skipped' => true,
        ]);
    }
    
    /**
     * Check and request feedback for a completed request
     */
    public function checkAndRequestFeedbackForRequest(ServiceRequest $request): void
    {
        if (in_array($request->status, ['completed', 'finalized']) && 
            !$request->feedback_requested_at && 
            !$request->feedback_skipped) {
            $this->requestFeedbackForRequest($request);
        }
    }
    
    /**
     * Mark feedback as provided for a request
     */
    public function markFeedbackProvidedForRequest(ServiceRequest $request): void
    {
        $request->update([
            'feedback_provided_at' => now(),
        ]);
    }
}
