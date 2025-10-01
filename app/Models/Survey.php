<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'title',
        'description',
        'questions',
        'settings',
        'is_active',
        'starts_at',
        'ends_at',
        'responses_count',
    ];

    protected $casts = [
        'questions' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Get the site that owns this survey
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get all responses for this survey
     */
    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    /**
     * Check if survey is currently active
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Increment responses count
     */
    public function incrementResponses(): void
    {
        $this->increment('responses_count');
    }

    /**
     * Get survey statistics
     */
    public function getStatistics(): array
    {
        $responses = $this->responses()->count();
        $uniqueSessions = $this->responses()->distinct('session_id')->count();
        
        return [
            'total_responses' => $responses,
            'unique_sessions' => $uniqueSessions,
            'completion_rate' => $responses > 0 ? round(($uniqueSessions / $responses) * 100, 2) : 0,
        ];
    }
}
