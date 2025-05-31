<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\IncidentReport;



class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'contact_number',
        'email',
        'purok_id',
        'password',
        'role',
        'is_approved',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    
    public function requests()
    {
        return $this->hasMany(\App\Models\Request::class);
    }

    public function incidentReports()
    {
        return $this->hasMany(IncidentReport::class);
    }
}
