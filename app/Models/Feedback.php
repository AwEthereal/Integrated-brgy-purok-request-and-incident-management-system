<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $fillable = [
        'user_id',
        'incident_report_id',
        'request_id',
        'sqd0_rating',
        'sqd1_rating',
        'sqd2_rating',
        'sqd3_rating',
        'sqd4_rating',
        'sqd5_rating',
        'sqd6_rating',
        'sqd7_rating',
        'sqd8_rating',
        'comments',
        'is_anonymous',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function getAverageRatingAttribute(): float
    {
        $ratings = [
            $this->sqd0_rating,
            $this->sqd1_rating,
            $this->sqd2_rating,
            $this->sqd3_rating,
            $this->sqd4_rating,
            $this->sqd5_rating,
            $this->sqd6_rating,
            $this->sqd7_rating,
            $this->sqd8_rating,
        ];

        return array_sum($ratings) / count($ratings);
    }
}
