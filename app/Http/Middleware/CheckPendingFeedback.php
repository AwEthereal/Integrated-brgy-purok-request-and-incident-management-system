<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FeedbackService;
use Symfony\Component\HttpFoundation\Response;

class CheckPendingFeedback
{
    protected $feedbackService;

    /**
     * Create a new middleware instance.
     */
    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated residents
        if (auth()->check() && auth()->user()->role === 'resident') {
            // Only check on non-AJAX requests to avoid performance issues
            if (!$request->ajax() && !$request->wantsJson()) {
                // Skip feedback check on certain routes to avoid loops
                $excludedRoutes = [
                    'feedback.store',
                    'feedback.submit',
                    'feedback.skip',
                    'logout'
                ];
                
                if (!in_array($request->route()->getName(), $excludedRoutes)) {
                    try {
                        $pendingFeedback = $this->feedbackService->getPendingFeedbackItem();
                        
                        if ($pendingFeedback) {
                            session([
                                'show_feedback_prompt' => true,
                                'pending_feedback' => $pendingFeedback
                            ]);
                            
                            \Log::info('Feedback prompt triggered', [
                                'user_id' => auth()->id(),
                                'type' => $pendingFeedback['type'],
                                'item_id' => $pendingFeedback['id']
                            ]);
                        }
                    } catch (\Exception $e) {
                        // Log error but don't break the request
                        \Log::error('Error checking pending feedback: ' . $e->getMessage());
                    }
                }
            }
        }

        return $next($request);
    }
}
