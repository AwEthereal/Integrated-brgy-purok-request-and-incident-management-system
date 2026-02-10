<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Authorize purok private channels
Broadcast::channel('purok.{purokId}', function (User $user, $purokId) {
    // Allow purok leaders and admins to listen to their purok's channel
    if ($user->purok_id == $purokId || in_array($user->role, ['admin', 'purok_leader'])) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'purok_id' => $purokId,
            'can_join' => true
        ];
    }
    
    // Log unauthorized access attempts
    \Log::warning('Unauthorized channel access attempt', [
        'user_id' => $user->id,
        'purok_id' => $purokId,
        'user_purok_id' => $user->purok_id,
        'role' => $user->role
    ]);
    
    return false;
});

// Authorize barangay officials channel for incident reports
Broadcast::channel('barangay-officials', function (User $user) {
    // Allow barangay officials and admins to listen to incident reports
    if (in_array($user->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'sk_chairman', 'admin'])) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'can_join' => true
        ];
    }
    
    return false;
});
