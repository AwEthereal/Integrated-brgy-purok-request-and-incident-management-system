<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Request extends Model
{
    protected $fillable = [
        'form_type',
        'purpose',
        'remarks',
        'status',
        'user_id',
        'purok_id',
        'purok_approved_at',
        'purok_approved_by',
        'barangay_approved_at',
        'barangay_approved_by',
        'document_generated_at',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purok()
    {
        return $this->belongsTo(Purok::class);
    }

    public function purokApprover()
    {
        return $this->belongsTo(User::class, 'purok_approved_by');
    }

    public function barangayApprover()
    {
        return $this->belongsTo(User::class, 'barangay_approved_by');
    }

    public function isPurokApproved()
    {
        return !empty($this->purok_approved_at);
    }

    public function isBarangayApproved()
    {
        return !empty($this->barangay_approved_at);
    }

    public function isDocumentGenerated()
    {
        return !empty($this->document_generated_at);
    }
}
