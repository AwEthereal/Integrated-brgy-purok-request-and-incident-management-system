<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purok extends Model
{
    protected $fillable = [
        'name',
        'description',
        'created_at',
        'updated_at'
    ];
    
    /**
     * Get the users for the purok.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
