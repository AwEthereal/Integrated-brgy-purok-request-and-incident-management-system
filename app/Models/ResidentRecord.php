<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResidentRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purok_id',
        'user_id',
        'philsys_card_no',
        'last_name', 'first_name', 'middle_name', 'suffix',
        'birth_date', 'birth_place',
        'sex', 'civil_status', 'religion', 'citizenship',
        'residence_address', 'occupation',
        'region', 'province', 'city_municipality', 'barangay',
        'contact_number', 'email',
        'highest_educ_attainment', 'educ_specify', 'is_graduate', 'is_undergraduate',
        'date_accomplished',
        'left_thumbmark_path', 'right_thumbmark_path', 'signature_path',
        'household_number', 'attested_by_user_id', 'is_locked',
        'status',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'date_accomplished' => 'date',
        'is_graduate' => 'boolean',
        'is_undergraduate' => 'boolean',
        'is_locked' => 'boolean',
    ];

    public function purok()
    {
        return $this->belongsTo(Purok::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attestedBy()
    {
        return $this->belongsTo(User::class, 'attested_by_user_id');
    }
}
