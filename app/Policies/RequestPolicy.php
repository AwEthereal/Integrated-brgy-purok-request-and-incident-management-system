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
        // Allow access to barangay officials, purok leaders, and admins
        return in_array($user->role, [
            'barangay_captain',
            'barangay_kagawad',
            'secretary',
            'barangay_clerk',
            'sk_chairman',
            'purok_leader',
            'admin'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Request $request): bool
    {
        // Debug information for policy check
        $debugInfo = [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'request_user_id' => $request->user_id,
            'request_status' => $request->status,
            'is_requester' => $user->id === $request->user_id,
            'is_purok_leader' => $user->role === 'purok_leader',
            'same_purok' => $user->purok_id == $request->purok_id,
            'is_barangay_official' => in_array($user->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'sk_chairman', 'admin']),
            'user_purok_id' => $user->purok_id ?? null,
            'request_purok_id' => $request->purok_id ?? null
        ];
        
        // Log the debug info
        \Log::debug('RequestPolicy@view - Authorization Check', $debugInfo);
        
        // Log the full user and request objects for debugging
        \Log::debug('RequestPolicy@view - User object', $user->toArray());
        \Log::debug('RequestPolicy@view - Request object', $request->toArray());

        // Allow if user is the requester
        if ($user->id === $request->user_id) {
            \Log::debug('RequestPolicy@view - Allowed: User is the requester');
            return true;
        }
        
        // Allow purok leaders to view requests from their purok
        if ($user->role === 'purok_leader' && $user->purok_id == $request->purok_id) {
            \Log::debug('RequestPolicy@view - Allowed: User is purok leader for this purok');
            return true;
        }
        
        // Allow barangay officials and admins to view all requests
        if (in_array($user->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'barangay_clerk', 'sk_chairman', 'admin'])) {
            \Log::debug('RequestPolicy@view - Allowed: User is barangay official or admin');
            return true;
        }
        
        // If none of the above conditions are met, deny access
        \Log::debug('RequestPolicy@view - Denied: No matching authorization rule');
        return false;
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
        return in_array($user->role, ['purok_leader', 'admin']);
    }

    // Purok Leader Methods
    public function viewPendingPurok(User $user): bool
    {
        // Purok leaders, purok presidents, and admins can view pending purok requests
        return in_array($user->role, ['purok_leader', 'admin']);
    }

    public function approvePurok(User $user, Request $request): bool
    {
        // Admins can approve at any time
        if ($user->role === 'admin') {
            return in_array($request->status, ['pending', 'purok_approved']);
        }

        // Secretary/Captain can approve pending requests at purok level
        if (in_array($user->role, ['secretary', 'barangay_captain'])) {
            return $request->status === 'pending';
        }

        // Purok leaders can approve requests in their purok
        // Allow approval for both 'pending' and 'purok_approved' statuses
        return ($user->role === 'purok_leader') &&
               $request->purok_id === $user->purok_id &&
               in_array($request->status, ['pending', 'purok_approved']);
    }

    // Barangay Official Methods
    public function viewPendingBarangay(User $user): bool
    {
        // Barangay officials and admins can view pending barangay requests
        return in_array($user->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'barangay_clerk', 'sk_chairman', 'admin']);
    }

    public function approveBarangay(User $user, Request $request): bool
    {
        // Only barangay officials can approve requests at barangay level
        return (in_array($user->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'barangay_clerk', 'sk_chairman', 'admin'])) &&
               $request->status === 'purok_approved';
    }

    public function complete(User $user, Request $request): bool
    {
        // Only barangay officials can mark requests as completed
        return (in_array($user->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'barangay_clerk', 'sk_chairman', 'admin'])) &&
               $request->status === 'barangay_approved';
    }

    public function reject(User $user, Request $request): bool
    {
        // Purok leaders and purok presidents can reject requests in their purok that are pending or purok_approved
        if ($user->role === 'purok_leader') {
            return $request->purok_id === $user->purok_id && 
                   in_array($request->status, ['pending', 'purok_approved']);
        }

        // Secretary/Captain can reject pending requests (purok-level)
        if (in_array($user->role, ['secretary', 'barangay_captain'])) {
            return in_array($request->status, ['pending', 'purok_approved', 'barangay_approved']);
        }
        
        // Barangay officials and admins can reject requests that are purok_approved or barangay_approved
        if (in_array($user->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'barangay_clerk', 'sk_chairman', 'admin'])) {
            return $request->status === 'purok_approved' || $request->status === 'barangay_approved';
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Request $request): bool
    {
        // Only the requester can delete their own request if it's pending or rejected
        return $user->id === $request->user_id && in_array($request->status, ['pending', 'rejected']);
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
