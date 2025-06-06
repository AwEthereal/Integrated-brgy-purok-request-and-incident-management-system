<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Request extends Model
{
    public const FORM_TYPES = [
        'barangay_clearance' => 'Barangay Clearance',
        'business_permit' => 'Business Permit',
        'certificate_of_residency' => 'Certificate of Residency',
        'certificate_of_indigency' => 'Certificate of Indigency',
        'other' => 'Other',
    ];

    protected $fillable = [
        'form_type',
        'purpose',
        'remarks',
        'status',
        'user_id',
        'purok_id',
        'purok_notes',
        'barangay_notes',
        'contact_number',
        'email',
        'birth_date',
        'gender',
        'civil_status',
        'occupation',
        'address_line1',
        'address_line2',
        'city',
        'province',
        'postal_code',
        'valid_id_front_path',
        'valid_id_back_path',
        'rejected_at',
        'rejected_by',
    ];

    protected $casts = [
        'purok_approved_at' => 'datetime',
        'barangay_approved_at' => 'datetime',
        'document_generated_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purok()
    {
        return $this->belongsTo(Purok::class);
    }
    
    public function feedback()
    {
        return $this->hasOne(\App\Models\Feedback::class, 'request_id');
    }

    public function purokApprover()
    {
        return $this->belongsTo(User::class, 'purok_approved_by');
    }

    public function barangayApprover()
    {
        return $this->belongsTo(User::class, 'barangay_approved_by');
    }
    
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function scopePendingPurokApproval($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePendingBarangayApproval($query)
    {
        return $query->where('status', 'purok_approved');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPurokApproved()
    {
        return $this->status === 'purok_approved';
    }

    public function isBarangayApproved()
    {
        return $this->status === 'barangay_approved';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
