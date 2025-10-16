<?php

namespace App\Http\Controllers;

use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BarangayApprovalController extends Controller
{
    // List all requests ready for barangay approval
    public function index(HttpRequest $request)
    {
        \Log::debug('BarangayApprovalController@index - Starting', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? 'guest',
            'can_barangay_actions' => auth()->user()->can('barangay-official-actions'),
            'request_query' => $request->query()
        ]);

        try {
            $this->authorize('barangay-official-actions');
            
            $puroks = \App\Models\Purok::orderBy('name')->get();
            $selectedPurok = $request->query('purok');

            // Get all requests that the user has permission to view
            $requestsQuery = Request::with(['user', 'purok', 'barangayApprover']);
                
            // Apply purok filter if selected
            if ($selectedPurok) {
                $requestsQuery->where('purok_id', $selectedPurok);
            }
            
            // Apply status filter if provided
            $status = $request->query('status');
            $showHistory = $status === 'completed';
            
            if ($showHistory) {
                // Apply status filter if provided
                $validStatuses = ['purok_approved', 'barangay_approved', 'in_progress', 'completed', 'rejected'];
                if ($request->has('request_status') && in_array($request->query('request_status'), $validStatuses)) {
                    $requestsQuery->where('status', $request->query('request_status'));
                } else {
                    $requestsQuery->whereIn('status', ['barangay_approved', 'rejected']);
                }
                
                // Add search functionality for history
                if ($request->has('search')) {
                    $search = $request->query('search');
                    $requestsQuery->where(function($query) use ($search) {
                        $query->where('id', 'like', "%{$search}%")
                              ->orWhere('purpose', 'like', "%{$search}%")
                              ->orWhere('status', 'like', "%{$search}%")
                              ->orWhereHas('user', function($q) use ($search) {
                                  $q->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%");
                              });
                    });
                }
                
                // Get the requests with pagination for history
                $requests = $requestsQuery->orderBy('updated_at', 'desc')
                    ->paginate(10)
                    ->withQueryString();
            } else {
                // Default to showing pending approvals
                $requestsQuery->where('status', 'purok_approved');
                
                // Get pending requests without pagination (for the approval list)
                $user = auth()->user();
                $requests = $requestsQuery->orderBy('created_at', 'desc')
                    ->get()
                    ->filter(function($request) use ($user) {
                        return $user->can('view', $request);
                    });
            }
            
            \Log::debug('BarangayApprovalController@index - Data loaded', [
                'requests_count' => $requests->count(),
                'puroks_count' => $puroks->count(),
                'selected_purok' => $selectedPurok
            ]);
            
            return view('barangay_official.approvals', compact('requests', 'puroks'));
            
        } catch (\Exception $e) {
            \Log::error('BarangayApprovalController@index - Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    // Show details for a specific request
    public function show($id)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                \Log::error('BarangayApprovalController@show - No authenticated user');
                return redirect()->route('login');
            }
            
            // Debug logging before loading the request
            \Log::debug('BarangayApprovalController@show - Starting', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'request_id' => $id
            ]);
            
            // First, find the request with minimal relationships to check permissions
            $request = Request::select([
                'id', 'user_id', 'purok_id', 'status', 'form_type', 'purpose', 'purok_notes',
                'valid_id_front_path', 'valid_id_back_path', 'created_at', 'purok_approved_by',
                'barangay_approved_by', 'barangay_rejected_at', 'barangay_rejection_reason'
            ])->find($id);
            
            if (!$request) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Request not found');
            }
            
            // Debug logging after loading basic request
            \Log::debug('BarangayApprovalController@show - Basic request loaded', [
                'request_id' => $request->id,
                'status' => $request->status,
                'purok_id' => $request->purok_id,
                'purok_approved_by' => $request->purok_approved_by,
                'barangay_approved_by' => $request->barangay_approved_by
            ]);
            
            // Check if user can view this request using the policy
            if (!Gate::allows('view', $request)) {
                \Log::warning('BarangayApprovalController@show - Unauthorized access attempt', [
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'request_id' => $id,
                    'request_status' => $request->status ?? 'unknown',
                    'request_purok_id' => $request->purok_id,
                    'user_purok_id' => $user->purok_id
                ]);
                return redirect()->route('barangay.approvals.index')
                    ->with('error', 'You are not authorized to view this request.');
            }
            
            // Now load the request with only the necessary relationships
            $request->load([
                'user:id,name,email,contact_number',
                'purok:id,name',
                'purokApprover:id,name,role',
                'barangayApprover:id,name,role',
                'purokLeader:id,name,role,purok_id'
            ]);
            
            // Debug logging after loading relationships
            \Log::debug('BarangayApprovalController@show - Request with relationships loaded', [
                'user_loaded' => $request->relationLoaded('user'),
                'purok_loaded' => $request->relationLoaded('purok'),
                'purok_approver_loaded' => $request->relationLoaded('purokApprover'),
                'barangay_approver_loaded' => $request->relationLoaded('barangayApprover'),
                'purok_leader_loaded' => $request->relationLoaded('purokLeader'),
                'purok_leader' => $request->purokLeader ? [
                    'id' => $request->purokLeader->id,
                    'name' => $request->purokLeader->name,
                    'role' => $request->purokLeader->role,
                    'purok_id' => $request->purokLeader->purok_id
                ] : null
            ]);
            
            // Log successful access
            \Log::info('BarangayApprovalController@show - Request accessed', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'request_id' => $id,
                'status' => $request->status,
                'purok_id' => $request->purok_id
            ]);
            
            // Return the view with the request data
            return view('barangay_official.show', compact('request'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('BarangayApprovalController@show - Request not found', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('barangay.approvals.index')
                ->with('error', 'Request not found.');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('BarangayApprovalController@show - Error', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // If it's an authorization error, show a more specific message
            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return redirect()->route('barangay.approvals.index')
                    ->with('error', 'You are not authorized to view this request. Please ensure you have the correct role and permissions.');
            }
            
            // For other errors, redirect with a generic error message
            return redirect()->route('barangay.approvals.index')
                ->with('error', 'An error occurred while processing your request.');
        }
    }

    // Approve a request
    public function approve(HttpRequest $httpRequest, $id)
    {
        $this->authorize('barangay-official-actions');
        $request = Request::findOrFail($id);
        if ($request->status !== 'purok_approved') {
            return back()->with('error', 'Request is not pending barangay approval.');
        }
        $request->status = 'barangay_approved';
        $request->barangay_approved_at = now();
        $request->barangay_approved_by = Auth::id();
        $request->save();
        
        // Send email notification to resident
        $request->user->notify(new \App\Notifications\RequestApprovedNotification($request, 'barangay'));
        
        return redirect()->route('barangay.approvals.index')->with('success', 'Request approved and notification sent to resident.');
    }

    // Reject a request
    public function reject(HttpRequest $httpRequest, $id)
    {
        $this->authorize('barangay-official-actions');
        $request = Request::findOrFail($id);
        if ($request->status !== 'purok_approved') {
            return back()->with('error', 'Request is not pending barangay approval.');
        }
        $request->status = 'rejected';
        $request->barangay_rejected_at = now();
        $request->barangay_rejected_by = Auth::id();
        $request->rejection_reason = $httpRequest->input('rejection_reason');
        $request->rejected_at = now();
        $request->rejected_by = Auth::id();
        $request->save();
        
        // Send email notification to resident
        $request->user->notify(new \App\Notifications\RequestRejectedNotification($request, 'barangay'));
        
        return redirect()->route('barangay.approvals.index')->with('success', 'Request rejected and notification sent to resident.');
    }
}
