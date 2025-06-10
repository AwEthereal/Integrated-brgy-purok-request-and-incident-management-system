<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use App\Models\User;
use App\Models\Purok;
use Illuminate\Support\Facades\Auth;

class PurokLeaderController extends Controller
{
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
