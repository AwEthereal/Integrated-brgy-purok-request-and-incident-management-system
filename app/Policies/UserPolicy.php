<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isPurokLeader() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Only Barangay Captain can edit Secretary's profile
        if ($model->role === 'secretary') {
            return $user->role === 'barangay_captain';
        }
        // Otherwise, only Secretary or Barangay Captain can edit profiles
        return in_array($user->role, ['barangay_captain', 'secretary']);
    }
    
    /**
     * Determine whether the user can approve a resident.
     */
    public function approveResident(User $user, User $resident): bool
    {
        // Only purok leaders can approve residents in their purok
        // Admins can also approve residents
        return $user->isAdmin() || 
               ($user->isPurokLeader() && $user->purok_id === $resident->purok_id);
    }
    
    /**
     * Determine whether the user can reject a resident.
     */
    public function rejectResident(User $user, User $resident): bool
    {
        // Only purok leaders can reject residents in their purok
        // Admins can also reject residents
        return $user->isAdmin() || 
               ($user->isPurokLeader() && $user->purok_id === $resident->purok_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
