<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\IncidentReport;
use App\Models\Request as ServiceRequest;
use App\Services\FeedbackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    protected $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

    public function general()
    {
        return $this->showFeedbackForm('general', null);
    }

    public function publicGeneral()
    {
        return view('public.feedback');
    }

    /**
     * Show feedback form for a specific request or incident
     */
    public function showFeedbackForm($type, $id = null)
    {
        $item = null;
        $title = 'Feedback';
        $description = 'Please share your feedback about this item.';
        
        if ($type === 'request' && $id) {
            $item = ServiceRequest::findOrFail($id);
            $title = 'Feedback for Request #' . $item->id;
            $description = 'Please share your feedback about your ' . ($item->form_type ?? 'service request') . '.';
        } elseif ($type === 'incident' && $id) {
            $item = IncidentReport::findOrFail($id);
            $title = 'Feedback for Incident #' . $item->id;
            $description = 'Please share your feedback about how we handled your ' . ($item->incident_type ?? 'incident report') . '.';
        } else {
            $title = 'General Feedback';
            $description = 'Please share your feedback about our services.';
        }

        return view('feedback.form', [
            'title' => $title,
            'description' => $description,
            'item' => $item,
            'itemType' => $type,
            'itemId' => $id,
        ]);
    }

    /**
     * Store the feedback submission
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request data
        $validated = $request->validate([
            'item_type' => 'required|in:request,incident,general',
            'item_id' => 'required_if:item_type,request,incident',
            'sqd0_rating' => 'required|integer|min:1|max:5',
            'sqd1_rating' => 'required|integer|min:1|max:5',
            'sqd2_rating' => 'required|integer|min:1|max:5',
            'sqd3_rating' => 'required|integer|min:1|max:5',
            'sqd4_rating' => 'required|integer|min:1|max:5',
            'sqd5_rating' => 'required|integer|min:1|max:5',
            'sqd6_rating' => 'required|integer|min:1|max:5',
            'sqd7_rating' => 'required|integer|min:1|max:5',
            'sqd8_rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
            'is_anonymous' => 'sometimes',
        ]);

        $isAnonymous = $request->boolean('is_anonymous');

        try {
            // Prepare feedback data
            $feedbackData = [
                'user_id' => (!$user || $isAnonymous) ? null : $user->id,
                'sqd0_rating' => $validated['sqd0_rating'],
                'sqd1_rating' => $validated['sqd1_rating'],
                'sqd2_rating' => $validated['sqd2_rating'],
                'sqd3_rating' => $validated['sqd3_rating'],
                'sqd4_rating' => $validated['sqd4_rating'],
                'sqd5_rating' => $validated['sqd5_rating'],
                'sqd6_rating' => $validated['sqd6_rating'],
                'sqd7_rating' => $validated['sqd7_rating'],
                'sqd8_rating' => $validated['sqd8_rating'],
                'comments' => $validated['comments'] ?? null,
                'is_anonymous' => $isAnonymous,
                'incident_report_id' => null,
                'request_id' => null,
            ];

            $redirectUrl = $request->boolean('is_public') ? url('/public') : route('dashboard');
            
            // Associate with the correct item and mark as completed
            if ($validated['item_type'] === 'request' && !empty($validated['item_id'])) {
                $requestItem = ServiceRequest::findOrFail($validated['item_id']);
                $feedbackData['request_id'] = $requestItem->id;
                
                // Mark feedback as provided
                $requestItem->update([
                    'feedback_provided_at' => now(),
                    'feedback_dismissed_at' => null
                ]);
                
                $redirectUrl = route('requests.show', $requestItem->id);
                $successMessage = 'Thank you for your feedback on your service request!';
                
            } elseif ($validated['item_type'] === 'incident' && isset($validated['item_id'])) {
                $incident = IncidentReport::findOrFail($validated['item_id']);
                $feedbackData['incident_report_id'] = $incident->id;
                
                // Mark feedback as provided
                $incident->update(['feedback_submitted_at' => now()]);
                
                $redirectUrl = route('incidents.show', $incident->id);
                $successMessage = 'Thank you for your feedback on your incident report!';
                
            } else {
                $redirectUrl = $request->boolean('is_public') ? url('/public') : route('dashboard');
                $successMessage = 'Thank you for your feedback! Your input helps us improve our services.';
            }

            // Create the feedback
            $feedback = Feedback::create($feedbackData);

            // Clear the feedback prompt from the session
            $request->session()->forget(['show_feedback_prompt', 'pending_feedback', 'feedback_checked']);
            
            // Set a cookie to prevent showing the feedback prompt again for a while
            return redirect($redirectUrl)
                ->with('status', 'Thank you for your feedback!')
                ->cookie('feedback_submitted', true, 60 * 24 * 7); // 1 week

            // Log successful submission if not anonymous
            if (!$isAnonymous) {
                Log::info('Feedback submitted successfully', [
                    'user_id' => $user->id,
                    'item_type' => $validated['item_type'],
                    'item_id' => $validated['item_id'] ?? null,
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors
            Log::error('Feedback validation failed', [
                'user_id' => $user->id,
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            
            // Re-throw the exception to show the validation errors to the user
            throw $e;
        } catch (\Exception $e) {
            // Log any other errors
            Log::error('Feedback submission failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'An error occurred while submitting your feedback. Please try again.');
        }
    }

    /**
     * Skip providing feedback for an item
     */
    /**
     * Handle AJAX feedback submission
     */
    public function submit(Request $request)
    {
        \Log::info('=== FEEDBACK SUBMISSION STARTED ===');
        \Log::info('Request data:', $request->all());
        
        try {
            if (!Auth::check()) {
                \Log::error('No authenticated user');
                return response()->json([
                    'message' => 'Authentication required',
                    'success' => false
                ], 401);
            }
            
            $user = Auth::user();
            \Log::info('Authenticated user:', ['id' => $user->id, 'name' => $user->name]);
            
            // Validate the request data
            $validated = $request->validate([
                'type' => 'required|in:request,incident',
                'id' => 'required|integer',
                'ratings' => 'required|array|size:9',
                'ratings.*' => 'required|integer|min:1|max:5',
                'comments' => 'nullable|string|max:1000',
                'is_anonymous' => 'sometimes',
            ]);

            $isAnonymous = $request->boolean('is_anonymous');
            
            \Log::info('Validation passed', $validated);
            
            // Ensure all ratings are present and valid
            $ratings = $validated['ratings'];
            for ($i = 0; $i < 9; $i++) {
                if (!isset($ratings[$i]) || $ratings[$i] < 1 || $ratings[$i] > 5) {
                    $ratings[$i] = 3; // Default to neutral if invalid
                    \Log::warning("Rating $i was invalid, defaulting to 3", ['value' => $ratings[$i] ?? 'not set']);
                }
            }
            try {
                // Prepare feedback data with all 9 SQD ratings
                $feedbackData = [
                    'user_id' => $isAnonymous ? null : $user->id,
                    'sqd0_rating' => $ratings[0] ?? 3,
                    'sqd1_rating' => $ratings[1] ?? 3,
                    'sqd2_rating' => $ratings[2] ?? 3,
                    'sqd3_rating' => $ratings[3] ?? 3,
                    'sqd4_rating' => $ratings[4] ?? 3,
                    'sqd5_rating' => $ratings[5] ?? 3,
                    'sqd6_rating' => $ratings[6] ?? 3,
                    'sqd7_rating' => $ratings[7] ?? 3,
                    'sqd8_rating' => $ratings[8] ?? 3,
                    'comments' => $validated['comments'] ?? null,
                    'is_anonymous' => $isAnonymous,
                ];
                
                \Log::info('Prepared feedback data:', $feedbackData);
                
                // Start database transaction
                \DB::beginTransaction();
                
                // Associate with the correct item
                if ($validated['type'] === 'request') {
                    $item = ServiceRequest::find($validated['id']);
                    if (!$item) {
                        throw new \Exception('Service request not found');
                    }
                    $feedbackData['request_id'] = $item->id;
                    $item->feedback_requested_at = now();
                    $item->save();
                    $redirectUrl = route('requests.show', $item->id);
                } else {
                    $item = IncidentReport::find($validated['id']);
                    if (!$item) {
                        throw new \Exception('Incident report not found');
                    }
                    $feedbackData['incident_report_id'] = $item->id;
                    $item->feedback_requested_at = now();
                    $item->save();
                    $redirectUrl = route('incident_reports.show', $item->id);
                }
                
                // Create the feedback
                $feedback = Feedback::create($feedbackData);
                \Log::info('Feedback created successfully', ['feedback_id' => $feedback->id]);
                
                // Commit transaction
                \DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Thank you for your feedback!',
                    'redirect' => $redirectUrl
                ]);
                
            } catch (\Exception $e) {
                // Rollback transaction on error
                $pdo = \DB::getPdo();
                if ($pdo && $pdo->inTransaction()) {
                    \DB::rollBack();
                }
                
                $errorMessage = 'Error submitting feedback: ' . $e->getMessage();
                \Log::error($errorMessage);
                \Log::error($e->getTraceAsString());
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error_details' => config('app.debug') ? [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ] : null
                ], 500);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Item not found for feedback', [
                'type' => $validated['type'] ?? 'unknown',
                'id' => $validated['id'] ?? null,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'The item you are trying to provide feedback for could not be found.'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Feedback submission failed', [
                'user_id' => $user->id ?? 'guest',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your feedback. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Skip providing feedback for an item
     */
    public function skip(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'nullable|in:request,incident',
            'item_id' => 'nullable|integer',
            'type' => 'nullable|in:request,incident',
            'id' => 'nullable|integer',
        ]);

        $itemType = $validated['item_type'] ?? $validated['type'] ?? null;
        $itemId = $validated['item_id'] ?? $validated['id'] ?? null;
        if (!$itemType || !$itemId) {
            return response()->json([
                'success' => false,
                'message' => 'Missing item reference.'
            ], 422);
        }

        try {
            if ($itemType === 'request') {
                $item = ServiceRequest::findOrFail($itemId);
                $this->feedbackService->skipFeedbackForRequest($item);
            } else {
                $item = IncidentReport::findOrFail($itemId);
                $this->feedbackService->skipFeedbackForIncident($item);
            }

            // Clear the feedback prompt from the session
            $request->session()->forget(['show_feedback_prompt', 'pending_feedback', 'feedback_checked']);

            // Set a cookie to prevent showing the feedback prompt again for a while
            return response()
                ->json(['success' => true, 'message' => 'Feedback skipped.'])
                ->cookie('feedback_skipped', true, 60 * 24); // 1 day
                
        } catch (\Exception $e) {
            Log::error('Error skipping feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to skip feedback. Please try again.'
            ], 500);
        }
    }
}
