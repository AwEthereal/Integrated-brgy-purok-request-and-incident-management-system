<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Purok;

class IncidentReport extends Model
{
    protected $fillable = [
        'user_id',
        'purok_id',
        'description',
        'photo_path',
        'latitude',
        'longitude',
        'location',
        'status',
        'staff_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purok()
    {
        return $this->belongsTo(Purok::class);
    }
}
