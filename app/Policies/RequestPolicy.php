<?php

namespace App\Policies;

use App\Models\Request;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Residents can view their own requests
        // Purok leaders and barangay officials can view requests in their jurisdiction
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Request $request)
    {
        // Allow if user is the requester, or has purok/barangay role
        return $user->id === $request->user_id || 
               $user->role === 'purok_leader' || 
               $user->role === 'barangay_official' ||
               $user->role === 'admin';
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only residents can create requests
        return $user->role === 'resident';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Request $request): bool
    {
        // Only the requester can update their own request if it's still pending
        return $user->id === $request->user_id && $request->status === 'pending';
    }

    // Purok Leader Methods
    public function viewPendingPurok(User $user): bool
    {
        // Purok leaders and admins can view pending purok requests
        return $user->role === 'purok_leader' || $user->role === 'admin';
    }

    public function approvePurok(User $user, Request $request): bool
    {
        // Only purok leaders can approve requests in their purok
        return $user->role === 'purok_leader' && 
               $request->purok_id === $user->purok_id &&
               $request->status === 'pending';
    }

    // Barangay Official Methods
    public function viewPendingBarangay(User $user): bool
    {
        // Barangay officials and admins can view pending barangay requests
        return $user->role === 'barangay_official' || $user->role === 'admin';
    }

    public function approveBarangay(User $user, Request $request): bool
    {
        // Only barangay officials can approve requests at barangay level
        return ($user->role === 'barangay_official' || $user->role === 'admin') &&
               $request->status === 'purok_approved';
    }

    public function complete(User $user, Request $request): bool
    {
        // Only barangay officials can mark requests as completed
        return ($user->role === 'barangay_official' || $user->role === 'admin') &&
               $request->status === 'barangay_approved';
    }

    public function reject(User $user, Request $request): bool
    {
        // Purok leaders and barangay officials can reject requests in their jurisdiction
        if ($user->role === 'purok_leader') {
            return $request->purok_id === $user->purok_id && 
                   ($request->status === 'pending' || $request->status === 'purok_approved');
        }
        
        if ($user->role === 'barangay_official' || $user->role === 'admin') {
            return $request->status === 'purok_approved' || $request->status === 'barangay_approved';
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Request $request): bool
    {
        return $user->id === $request->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Request $request): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Request $request): bool
    {
        return false;
    }
}
