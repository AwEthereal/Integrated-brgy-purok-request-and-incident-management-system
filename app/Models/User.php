<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\IncidentReport;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
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
        'email',
        'contact_number',
        'purok_id',
        'password',
        'role',
        'is_approved',
        'birth_date',
        'gender',
        'civil_status',
        'occupation',
        'address'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'birth_date' => 'date',
        'is_approved' => 'boolean',
        'email_verified_at' => 'datetime',
    ];
    
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
     * Send the email verification notification.
     *
     * @return void
     */
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
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
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
