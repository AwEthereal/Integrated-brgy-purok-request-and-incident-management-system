<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurokChangeRequest extends Model
{
    protected $fillable = [
        'user_id',
        'current_purok_id',
        'requested_purok_id',
        'status',
        'rejection_reason',
        'requested_at',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentPurok(): BelongsTo
    {
        return $this->belongsTo(Purok::class, 'current_purok_id');
    }

    public function requestedPurok(): BelongsTo
    {
        return $this->belongsTo(Purok::class, 'requested_purok_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
