<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Authorize purok private channels
Broadcast::channel('purok.{purokId}', function (User $user, $purokId) {
    // Allow purok leaders and admins to listen to their purok's channel
    if ($user->purok_id == $purokId || $user->hasRole('admin') || $user->hasRole('purok_leader')) {
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
        'roles' => $user->getRoleNames()->toArray()
    ]);
    
    return false;
});
