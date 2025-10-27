<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Purok;

class IncidentReport extends Model
{
    // Status constants for database storage
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_INVALID = 'invalid';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    
    /**
     * Get all available statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_APPROVED => 'Resolved', // Display as "Resolved" for consistency
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_INVALID => 'Invalid Report'
        ];
    }
    
    /**
     * Get the display status for resident view
     * Shows 'Pending' if not yet viewed, otherwise shows the actual status
     */
    public function getDisplayStatusForResident()
    {
        if ($this->status === self::STATUS_PENDING && !$this->viewed_at) {
            return 'Pending';
        }
        
        return self::getStatuses()[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }
    
    protected $fillable = [
        'user_id',
        'purok_id',
        'incident_type',
        'description',
        'photo_path',
        'photo_paths',
        'latitude',
        'longitude',
        'location',
        'status',
        'staff_notes',
        'satisfaction_rating',
        'feedback_comment',
        'is_anonymous',
        'feedback_submitted_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        // CSM Feedback Fields
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
    ];
    
    public const TYPES = [
        'crime' => 'Crime',
        'accident' => 'Accident',
        'natural_disaster' => 'Natural Disaster',
        'medical_emergency' => 'Medical Emergency',
        'fire' => 'Fire',
        'public_disturbance' => 'Public Disturbance',
        'traffic_incident' => 'Traffic Incident',
        'missing_person' => 'Missing Person',
        'environmental_hazard' => 'Environmental Hazard',
        'other' => 'Other',
    ];

    protected $casts = [
        'incident_type' => 'string',
        'photo_paths' => 'array',
        'is_anonymous' => 'boolean',
        'feedback_submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'sqd0_rating' => 'integer',
        'sqd1_rating' => 'integer',
        'sqd2_rating' => 'integer',
        'sqd3_rating' => 'integer',
        'sqd4_rating' => 'integer',
        'sqd5_rating' => 'integer',
        'sqd6_rating' => 'integer',
        'sqd7_rating' => 'integer',
        'sqd8_rating' => 'integer',
    ];

    /**
     * Check if feedback has been submitted
     */
    public function hasFeedback()
    {
        return !is_null($this->feedback_submitted_at);
    }
    
    /**
     * Get the average rating across all SQD questions
     */
    public function getAverageRatingAttribute()
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
        
        // Filter out null values
        $validRatings = array_filter($ratings, function($rating) {
            return !is_null($rating);
        });
        
        if (empty($validRatings)) {
            return null;
        }
        
        return array_sum($validRatings) / count($validRatings);
    }
    
    /**
     * Get the rating description based on the value
     */
    public static function getRatingDescription($rating)
    {
        $descriptions = [
            1 => 'Strongly Disagree',
            2 => 'Disagree',
            3 => 'Neutral',
            4 => 'Agree',
            5 => 'Strongly Agree',
        ];
        
        return $descriptions[$rating] ?? 'Not Rated';
    }
    
    /**
     * Get the rating emoji based on the value
     */
    public static function getRatingEmoji($rating)
    {
        $emojis = [
            1 => '😠', // Angry
            2 => '🙁', // Sad
            3 => '😐', // Neutral
            4 => '🙂', // Happy
            5 => '😊', // Very Happy
        ];
        
        return $emojis[$rating] ?? '';
    }

    /**
     * Scope a query to only include reports with feedback
     */
    public function scopeWithFeedback($query)
    {
        return $query->whereNotNull('feedback_submitted_at');
    }

    /**
     * Get the satisfaction rating as stars
     */
    public function getRatingStarsAttribute()
    {
        if (is_null($this->satisfaction_rating)) {
            return '';
        }
        
        $stars = '';
        $fullStars = $this->satisfaction_rating;
        $emptyStars = 5 - $fullStars;
        
        // Add full stars
        $stars .= str_repeat('★', $fullStars);
        // Add empty stars
        $stars .= str_repeat('☆', $emptyStars);
        
        return $stars;
    }

    protected $attributes = [
        'incident_type' => 'other',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function feedback()
    {
        return $this->hasOne(\App\Models\Feedback::class, 'incident_report_id');
    }

    public function purok()
    {
        return $this->belongsTo(Purok::class);
    }
    
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
    
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
