<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\IncidentReport;
use App\Models\ResidentRecord;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'username',
        'contact_number',
        'purok_id',
        'password',
        'role',
        'is_approved',
        'is_dummy',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'birth_date',
        'date_of_birth',
        'place_of_birth',
        'sex',
        'gender',
        'civil_status',
        'nationality',
        'occupation',
        'house_number',
        'street',
        'address'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'birth_date' => 'date',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user's abilities/permissions.
     *
     * @return array
     */
    public function getAbilities()
    {
        // Define base abilities based on user role
        $abilities = [];
        
        // Add role-based abilities
        if ($this->role === 'admin') {
            $abilities = ['*'];
        } else {
            // Add abilities based on role
            $roleAbilities = [
                'barangay_captain' => ['view_dashboard', 'manage_requests', 'approve_requests', 'reject_requests', 'view_reports'],
                'barangay_kagawad' => ['view_dashboard', 'view_requests', 'approve_requests', 'view_reports'],
                'secretary' => ['view_dashboard', 'manage_requests', 'view_reports'],
                'sk_chairman' => ['view_dashboard', 'view_requests', 'view_reports'],
                'barangay_clerk' => [],
                'purok_leader' => ['view_dashboard', 'view_own_purok_requests', 'approve_own_purok_requests'],
                'resident' => ['view_own_requests', 'create_requests'],
            ];
            
            $abilities = $roleAbilities[$this->role] ?? [];
        }
        
        return $abilities;
    }
    
    /**
     * Check if user is an admin
     *
     * @return bool
     */
    /**
     * Check if user is an admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin' || $this->role === 'administrator' || $this->role === 'barangay_captain';
    }

    /**
     * Helper: treat barangay_captain as admin-level access
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['admin', 'administrator', 'barangay_captain'], true);
    }
    
    /**
     * Check if user is a purok leader
     *
     * @return bool
     */
    /**
     * Check if user is a purok leader or purok president
     *
     * @return bool
     */
    public function isPurokLeader()
    {
        return $this->role === 'purok_leader';
    }
    
    /**
     * Get the user who approved this resident
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /**
     * Get the user who rejected this resident
     */
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
    
    /**
     * Get the purok that the user belongs to.
     */
    public function purok(): BelongsTo
    {
        return $this->belongsTo(Purok::class);
    }

    /**
     * Update the user's email and require verification.
     *
     * @param  string  $email
     * @return void
     */
    public function updateEmail(string $email)
    {
        if ($email !== $this->email) {
            $this->email = $email;
            $this->email_verified_at = null;
            $this->save();
            
            // Send verification email
            $this->sendEmailVerificationNotification();
        }
    }
    
    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }
    
    /**
     * Determine if the user has verified their email address.
     * Admin and officials are automatically considered verified.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        // Roles that don't require email verification
        $exemptRoles = [
            'admin',
            'barangay_captain',
            'barangay_kagawad',
            'barangay_clerk',
            'secretary',
            'sk_chairman',
            'purok_leader',
        ];

        // Exempt roles are automatically verified
        if (in_array($this->role, $exemptRoles)) {
            return true;
        }

        // For residents, check actual verification status
        return ! is_null($this->email_verified_at);
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification);
    }
    
    /**
     * Get the requests for the user.
     */
    public function requests()
    {
        return $this->hasMany(\App\Models\Request::class);
    }
    
    /**
     * Get the incident reports for the user.
     */
    public function incidentReports()
    {
        return $this->hasMany(IncidentReport::class);
    }
    
    public function feedback()
    {
        return $this->hasMany(\App\Models\Feedback::class);
    }
    
    /**
     * Resident records filled by Purok Leaders.
     */
    public function residentRecords()
    {
        return $this->hasMany(\App\Models\ResidentRecord::class);
    }

    public function latestResidentRecord()
    {
        return $this->hasOne(ResidentRecord::class)->latestOfMany();
    }
    
    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
    
    /**
     * Display-friendly role label (map captain to Admin)
     */
    public function getRoleDisplayAttribute(): string
    {
        if (in_array($this->role, ['admin', 'administrator', 'barangay_captain'], true)) {
            return 'Admin';
        }
        if ($this->role === 'purok_leader') {
            return 'Purok Leader';
        }
        return ucwords(str_replace('_', ' ', (string) $this->role));
    }
    
    /**
     * Get the user's full address.
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $address = $this->address;
        if ($this->purok) {
            $address .= ", Purok {$this->purok->name}";
        }
        return $address;
    }
}
