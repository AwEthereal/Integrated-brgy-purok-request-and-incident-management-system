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
               in_array($user->role, ['purok_leader', 'purok_president']) || 
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

    /**
     * Determine whether the user can update private notes of the request.
     */
    public function updatePrivateNotes(User $user, Request $request): bool
    {
        // Purok leaders, purok presidents, and admins can update private notes
        return in_array($user->role, ['purok_leader', 'purok_president', 'admin']);
    }

    // Purok Leader Methods
    public function viewPendingPurok(User $user): bool
    {
        // Purok leaders, purok presidents, and admins can view pending purok requests
        return in_array($user->role, ['purok_leader', 'purok_president', 'admin']);
    }

    public function approvePurok(User $user, Request $request): bool
    {
        // Purok leaders and purok presidents can approve requests in their purok
        return in_array($user->role, ['purok_leader', 'purok_president']) && 
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
        // Purok leaders and purok presidents can only reject pending requests in their purok
        if (in_array($user->role, ['purok_leader', 'purok_president'])) {
            return $request->purok_id === $user->purok_id && $request->status === 'pending';
        }
        
        // Barangay officials and admins can reject requests that are purok_approved or barangay_approved
        if (in_array($user->role, ['barangay_official', 'admin'])) {
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
