<?php

namespace App\Http\Controllers;

use App\Models\PurokChangeRequest;
use App\Models\Request as RequestModel;
use App\Models\User;
use App\Models\ResidentRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurokLeaderController extends Controller
{
    /**
     * Show the purok leader dashboard with purok change requests
     * where the current user is the future purok leader
     */
    public function purokChangeRequests()
    {
        $user = Auth::user();
        $status = request()->input('status', 'pending');
        
        // Get purok change requests where the current user is the future purok leader
        $query = PurokChangeRequest::with([
            'user' => function($query) {
                $query->select('id', 'name', 'email', 'contact_number', 'address');
            },
            'currentPurok',
            'requestedPurok',
            'processedBy'
        ])
        ->where('requested_purok_id', $user->purok_id);
        
        // Apply status filter
        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        } else {
            $query->where('status', 'pending'); // Default to pending
        }
        
        $changeRequests = $query->latest()->paginate(10);
        
        // Get counts for each status
        $pendingCount = PurokChangeRequest::where('requested_purok_id', $user->purok_id)
            ->where('status', 'pending')
            ->count();
            
        $approvedCount = PurokChangeRequest::where('requested_purok_id', $user->purok_id)
            ->where('status', 'approved')
            ->count();
            
        $rejectedCount = PurokChangeRequest::where('requested_purok_id', $user->purok_id)
            ->where('status', 'rejected')
            ->count();
            
        return view('purok_leader.purok_change_requests', [
            'changeRequests' => $changeRequests,
            'purokName' => $user->purok ? $user->purok->name : 'Unknown Purok',
            'currentStatus' => $status,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount
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
            
            // Cancel any other pending purok change requests for this user
            $pendingPurokChangeRequests = PurokChangeRequest::where('user_id', $user->id)
                ->where('id', '!=', $changeRequest->id)
                ->where('status', 'pending')
                ->get();
            
            $canceledCount = 0;
            
            foreach ($pendingPurokChangeRequests as $request) {
                $request->update([
                    'status' => 'rejected',
                    'rejection_reason' => 'Automatically rejected - another purok change was approved',
                    'processed_at' => now(),
                    'processed_by' => auth()->id()
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
            
            return redirect()->route('purok_leader.purok-change-requests', ['status' => 'approved'])->with('success', $message);
            
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
            
            return redirect()->route('purok_leader.purok-change-requests', ['status' => 'rejected'])->with('success', 'Purok change request has been rejected.');
            
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

        $purokApprovedStatuses = ['purok_approved', 'completed', 'approved'];
        $approvedStatuses = ['purok_approved', 'barangay_approved', 'completed', 'approved'];
        
        // Get the filter from the request
        $filter = request()->query('filter');
        $filterValue = request()->query('value');
        
        // Get search and filter parameters
        $search = request()->query('search');
        $statusFilter = request()->query('status_filter');
        $formTypeFilter = request()->query('form_type_filter');
        if ($formTypeFilter === null || $formTypeFilter === '') {
            $formTypeFilter = 'barangay_clearance';
        }
        $dateFrom = request()->query('date_from');
        $dateTo = request()->query('date_to');
        $sortBy = request()->query('sort_by', 'created_at');
        $sortOrder = request()->query('sort_order', 'desc');

        // Base query for requests
        $query = RequestModel::where('purok_id', $purokId)
            ->with('user');
            
        // Apply legacy filters if present (for backward compatibility)
        if ($filter && $filterValue) {
            switch ($filter) {
                case 'status':
                    if ($filterValue === 'all') {
                        // Show all requests (no status filter)
                    } else if ($filterValue === 'approved') {
                        $query->whereIn('status', $approvedStatuses);
                    } else if (in_array($filterValue, ['pending', 'rejected', 'cancelled', 'purok_approved', 'barangay_approved'])) {
                        if ($filterValue === 'purok_approved') {
                            $query->whereIn('status', $purokApprovedStatuses);
                        } else {
                            $query->where('status', $filterValue);
                        }
                    }
                    break;
                case 'resident':
                    $query->where('user_id', $filterValue);
                    break;
            }
        }
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhere('purpose', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%')
                                ->orWhere('address', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Apply status filter
        if ($statusFilter && $statusFilter !== 'all') {
            if ($statusFilter === 'approved') {
                $query->whereIn('status', $approvedStatuses);
            } else if ($statusFilter === 'purok_approved') {
                $query->whereIn('status', $purokApprovedStatuses);
            } else {
                $query->where('status', $statusFilter);
            }
        }
        
        // Apply form type filter
        if ($formTypeFilter && $formTypeFilter !== 'all') {
            $query->where('form_type', $formTypeFilter);
        }
        
        // Apply date range filter
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        // Apply sorting
        $allowedSortFields = ['created_at', 'id', 'status', 'form_type'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }
        
        // Get the filtered requests with pagination (10 per page)
        $perPage = 10;
        $requests = $query->paginate($perPage);
        
        // Preserve all query parameters in pagination links
        $requests->appends(request()->query());
        
        // Get all form types for filter dropdown
        $formTypes = RequestModel::FORM_TYPES;
        $formTypes['barangay_clearance'] = 'Purok Clearance';
            
        // Get statistics
        $rbiRecordsQuery = ResidentRecord::query()
            ->where('purok_id', $purokId)
            ->whereNull('deleted_at');

        $rbiLinkedUsersCount = (clone $rbiRecordsQuery)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        $rbiUnlinkedCount = (clone $rbiRecordsQuery)
            ->whereNull('user_id')
            ->count();

        $residentUsersWithoutRbiCount = User::query()
            ->where('purok_id', $purokId)
            ->where('role', 'resident')
            ->whereDoesntHave('residentRecords', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->count();

        $totalResidentsInPurok = $rbiLinkedUsersCount + $rbiUnlinkedCount + $residentUsersWithoutRbiCount;

        $statsRequestsQuery = RequestModel::query()->where('purok_id', $purokId);
        if ($formTypeFilter && $formTypeFilter !== 'all') {
            $statsRequestsQuery->where('form_type', $formTypeFilter);
        }

        $stats = [
            'total_requests' => (clone $statsRequestsQuery)->count(),
            'pending_requests' => (clone $statsRequestsQuery)
                                        ->where('status', 'pending')
                                        ->count(),
            'approved_requests' => (clone $statsRequestsQuery)
                                        ->whereIn('status', $approvedStatuses)
                                        ->count(),
            'purok_approved_requests' => (clone $statsRequestsQuery)
                                        ->whereIn('status', $purokApprovedStatuses)
                                        ->count(),
            'barangay_approved_requests' => (clone $statsRequestsQuery)
                                        ->where('status', 'barangay_approved')
                                        ->count(),
            'rejected_requests' => (clone $statsRequestsQuery)
                                        ->where('status', 'rejected')
                                        ->count(),
            'residents_count' => $totalResidentsInPurok,
            'pending_residents' => User::where('purok_id', $purokId)
                                ->where('role', 'resident')
                                ->where('is_approved', false)
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

        return view('purok_leader.dashboard', compact(
            'requests', 
            'stats', 
            'purokName', 
            'residents', 
            'activeFilter', 
            'pendingCount',
            'formTypes',
            'search',
            'statusFilter',
            'formTypeFilter',
            'dateFrom',
            'dateTo',
            'sortBy',
            'sortOrder'
        ));
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
