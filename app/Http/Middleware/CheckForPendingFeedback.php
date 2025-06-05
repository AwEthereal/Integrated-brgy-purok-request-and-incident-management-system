<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Services\FeedbackService;

class CheckForPendingFeedback
{
    protected $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Skip for non-GET requests, AJAX, API routes, or if user is not authenticated
            if (!$request->isMethod('get') || $request->ajax() || $request->is('api/*') || !Auth::check()) {
                return $next($request);
            }

            // Skip if we're already on the feedback pages
            if ($request->is('feedback/*')) {
                return $next($request);
            }
            
            // Only check for feedback on the dashboard or main pages
            if (!$request->is('/') && !$request->is('dashboard*') && !$request->is('home')) {
                return $next($request);
            }
            
            // Check if user has already submitted or skipped feedback in this session
            if (session()->has('feedback_shown') || 
                $request->cookie('feedback_submitted') || 
                $request->cookie('feedback_skipped')) {
                return $next($request);
            }
            
            // Check for pending feedback
            $pendingFeedback = $this->feedbackService->getPendingFeedbackItem();
            
            if ($pendingFeedback) {
                Log::info('Showing feedback prompt for pending feedback', ['item' => $pendingFeedback['type'], 'id' => $pendingFeedback['id']]);
                
                // Store the pending feedback in the session for the popup
                session([
                    'show_feedback_prompt' => true,
                    'pending_feedback' => $pendingFeedback,
                    'feedback_shown' => true
                ]);
            } else {
                // Clear any existing feedback prompt data
                session()->forget(['show_feedback_prompt', 'pending_feedback']);
            }
            
            return $next($request);
            
        } catch (\Exception $e) {
            Log::error('Error in CheckForPendingFeedback: ' . $e->getMessage());
            return $next($request);
        }
    }
}
