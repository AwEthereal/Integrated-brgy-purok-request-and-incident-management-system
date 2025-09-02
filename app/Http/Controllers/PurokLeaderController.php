<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use App\Models\User;
use App\Models\Purok;
use App\Models\PurokChangeRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurokLeaderController extends Controller
{
    /**
     * Show the purok leader dashboard with pending purok change requests
     * where the current user is the future purok leader
     */
    public function purokChangeRequests()
    {
        $user = Auth::user();
        
        // Get pending purok change requests where the current user is the future purok leader
        $changeRequests = PurokChangeRequest::with([
            'user' => function($query) {
                $query->select('id', 'name', 'email', 'contact_number', 'address');
            },
            'currentPurok',
            'requestedPurok'
        ])
        ->where('requested_purok_id', $user->purok_id)
        ->where('status', 'pending')
        ->latest()
        ->paginate(10);
            
        return view('purok_leader.purok_change_requests', [
            'changeRequests' => $changeRequests,
            'purokName' => $user->purok ? $user->purok->name : 'Unknown Purok'
        ]);
    }
    
    /**
     * Approve a purok change request
     * Only the future purok leader can approve the request
     */
    public function approvePurokChange(PurokChangeRequest $changeRequest)
    {
        // Verify the current user is the future purok leader
        if (auth()->user()->purok_id !== $changeRequest->requested_purok_id) {
            return back()->with('error', 'You are not authorized to approve this request.');
        }

        try {
            DB::beginTransaction();
            
            // Get the user and their old purok
            $user = $changeRequest->user;
            $oldPurokId = $user->purok_id;
            
            // Cancel any pending requests from the old purok
            $pendingRequests = RequestModel::where('user_id', $user->id)
                ->where('purok_id', $oldPurokId)
                ->whereIn('status', ['pending', 'purok_approved'])
                ->get();
            
            $canceledCount = 0;
            
            foreach ($pendingRequests as $request) {
                // Update each request individually to avoid raw SQL issues
                $request->update([
                    'status' => 'rejected',
                    'rejection_reason' => 'Automatically rejected due to purok change',
                    'rejected_at' => now(),
                    'rejected_by' => auth()->id(),
                    'purok_notes' => trim(($request->purok_notes ?? '') . "\n[Auto-rejected due to purok change on " . now()->format('Y-m-d H:i:s') . ' - ' . $changeRequest->requestedPurok->name . ' purok]')
                ]);
                $canceledCount++;
            }
            
            // Update the user's purok
            $user->purok_id = $changeRequest->requested_purok_id;
            $user->save();
            
            // Update the change request
            $changeRequest->update([
                'status' => 'approved',
                'processed_at' => now(),
                'processed_by' => auth()->id()
            ]);
            
            // Optionally, notify the user that their request was approved and about any canceled requests
            // $user->notify(new PurokChangeApproved($changeRequest, $canceledCount));
            
            DB::commit();
            
            $message = 'Purok change request approved successfully. ';
            $message .= 'The resident has been added to your purok.';
            
            if ($canceledCount > 0) {
                $message .= ' Changing to a different purok has canceled all pending requests.';
            }
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving purok change request: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve purok change request: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject a purok change request
     * Only the future purok leader can reject the request
     */
    public function rejectPurokChange(Request $request, PurokChangeRequest $changeRequest)
    {
        // Verify the current user is the future purok leader
        if (auth()->user()->purok_id !== $changeRequest->requested_purok_id) {
            return back()->with('error', 'You are not authorized to reject this request.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);
        
        try {
            $changeRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'processed_at' => now(),
                'processed_by' => auth()->id()
            ]);
            
            // Optionally, notify the user that their request was rejected
            // Notification::send($changeRequest->user, new PurokChangeRejected($changeRequest));
            
            return back()->with('success', 'Purok change request has been rejected.');
            
        } catch (\Exception $e) {
            Log::error('Error rejecting purok change request: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject purok change request.');
        }
    }
    
    // Show the purok leader dashboard
    public function dashboard()
    {
        $user = Auth::user()->load('purok');
        $purokId = $user->purok_id;
        $purokName = $user->purok ? $user->purok->name : 'Unknown Purok';
        
        // Get the filter from the request
        $filter = request()->query('filter');
        $filterValue = request()->query('value');

        // Base query for requests
        $query = RequestModel::where('purok_id', $purokId)
            ->with('user');
            
        // Apply filters if present
        if ($filter && $filterValue) {
            switch ($filter) {
                case 'status':
                    if ($filterValue === 'approved') {
                        $query->whereIn('status', ['purok_approved', 'completed']);
                    } else if (in_array($filterValue, ['pending', 'rejected'])) {
                        $query->where('status', $filterValue);
                    }
                    break;
                case 'resident':
                    $query->where('user_id', $filterValue);
                    break;
            }
        }
        
        // Get the filtered requests with pagination (10 per page)
        $perPage = 10;
        $requests = $query->latest()->paginate($perPage);
        
        // Add filter parameters to pagination links
        if (request()->has('filter') && request()->has('value')) {
            $requests->appends([
                'filter' => request('filter'),
                'value' => request('value')
            ]);
        }
            
        // Get statistics
        $stats = [
            'total_requests' => RequestModel::where('purok_id', $purokId)->count(),
            'pending_requests' => RequestModel::where('purok_id', $purokId)
                                        ->where('status', 'pending')
                                        ->count(),
            'approved_requests' => RequestModel::where('purok_id', $purokId)
                                        ->whereIn('status', ['purok_approved', 'completed'])
                                        ->count(),
            'rejected_requests' => RequestModel::where('purok_id', $purokId)
                                        ->where('status', 'rejected')
                                        ->count(),
            'residents_count' => User::where('purok_id', $purokId)
                                ->where('role', 'resident')
                                ->count(),
        ];

        // Get residents for the dashboard (limited to 5)
        $residents = User::where('purok_id', $purokId)
            ->where('role', 'resident')
            ->withCount(['requests' => function($query) {
                $query->where('purok_id', Auth::user()->purok_id);
            }])
            ->orderBy('last_name')
            ->take(5)
            ->get();
            
        // Get the active filter for the view
        $activeFilter = $filter ? ['type' => $filter, 'value' => $filterValue] : null;
        
        // Get pending requests count for the notification badge
        $pendingCount = RequestModel::where('purok_id', $purokId)
            ->where('status', 'pending')
            ->count();

        return view('purok_leader.dashboard', compact('requests', 'stats', 'purokName', 'residents', 'activeFilter', 'pendingCount'));
    }
    
    /**
     * Show all residents in the purok leader's purok
     */
    public function residents()
    {
        $user = Auth::user();
        $purokName = $user->purok ? $user->purok->name : 'Unknown Purok';
        
        // Get residents with their request counts and approval status
        $residents = User::where('purok_id', $user->purok_id)
            ->where('role', 'resident')
            ->withCount(['requests' => function($query) use ($user) {
                $query->where('purok_id', $user->purok_id);
            }])
            ->orderBy('is_approved')
            ->orderBy('last_name')
            ->paginate(15);
            
        return view('purok_leader.residents', compact('residents', 'purokName'));
    }
    
    /**
     * Show details of a specific resident
     */
    public function showResident($id)
    {
        $user = Auth::user();
        $purokName = $user->purok ? $user->purok->name : 'Unknown Purok';
        
        $resident = User::where('id', $id)
            ->where('purok_id', $user->purok_id)
            ->where('role', 'resident')
            ->with(['requests' => function($query) {
                $query->latest();
            }])
            ->firstOrFail();
            
        return view('purok_leader.resident_show', compact('resident', 'purokName'));
    }
}
