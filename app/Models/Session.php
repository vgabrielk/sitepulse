<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    use HasFactory;

    protected $table = 'analytics_sessions';

    protected $fillable = [
        'site_id',
        'session_token',
        'visitor_id',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'device_info',
        'started_at',
        'last_activity_at',
        'ended_at',
        'duration_seconds',
    ];

    protected $casts = [
        'device_info' => 'array',
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Get the site that owns this session
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get all visits for this session
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * Get all reviews for this session
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get all survey responses for this session
     */
    public function surveyResponses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    /**
     * Generate unique session token
     */
    public static function generateSessionToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Check if session is active
     */
    public function isActive(): bool
    {
        return is_null($this->ended_at) && 
               $this->last_activity_at->diffInMinutes(now()) <= 30;
    }

    /**
     * End the session
     */
    public function endSession(): void
    {
        $this->update([
            'ended_at' => now(),
            'duration_seconds' => $this->started_at->diffInSeconds(now()),
        ]);
    }

    /**
     * Update last activity
     */
    public function updateActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }
}
