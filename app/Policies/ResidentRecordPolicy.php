<?php

namespace App\Policies;

use App\Models\ResidentRecord;
use App\Models\User;

class ResidentRecordPolicy
{
    public function before(User $user, string $ability)
    {
        if (in_array($user->role, ['admin'])) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['purok_leader','secretary','barangay_clerk','barangay_captain','barangay_kagawad']);
    }

    public function view(User $user, ResidentRecord $record): bool
    {
        if (in_array($user->role, ['secretary','barangay_clerk','barangay_captain','barangay_kagawad'])) return true;
        return ($user->role === 'purok_leader') && ($user->purok_id === $record->purok_id);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['purok_leader','secretary','barangay_clerk','barangay_captain','barangay_kagawad']);
    }

    public function update(User $user, ResidentRecord $record): bool
    {
        if (in_array($user->role, ['secretary','barangay_clerk','barangay_captain','barangay_kagawad'])) return true;
        if ($record->is_locked) return false;
        return ($user->role === 'purok_leader') && ($user->purok_id === $record->purok_id);
    }

    public function delete(User $user, ResidentRecord $record): bool
    {
        if (in_array($user->role, ['secretary','barangay_clerk','barangay_captain','barangay_kagawad'])) return true;
        if ($record->is_locked) return false;
        return ($user->role === 'purok_leader') && ($user->purok_id === $record->purok_id);
    }
}
