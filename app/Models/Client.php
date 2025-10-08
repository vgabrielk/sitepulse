<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'api_key',
        'webhook_url',
        'webhook_secret',
        'plan',
        'plan_limits',
        'settings',
        'is_active',
        'trial_ends_at',
        'subscription_ends_at',
    ];

    protected $casts = [
        'plan_limits' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    /**
     * Get all sites for this client
     */
    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    /**
     * Get the user associated with this client
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Check if client is on trial
     */
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if client has active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }

    /**
     * Get plan limits
     */
    public function getPlanLimits(): array
    {
        // Start with stored limits or empty
        $storedLimits = is_array($this->plan_limits) ? $this->plan_limits : [];

        // Backward compatibility: map legacy monthly_visits -> monthly_sessions
        if (isset($storedLimits['monthly_visits']) && !isset($storedLimits['monthly_sessions'])) {
            $storedLimits['monthly_sessions'] = $storedLimits['monthly_visits'];
            unset($storedLimits['monthly_visits']);
        }

        // Remove deprecated keys if present
        unset($storedLimits['reviews']);

        // Merge with defaults so all keys exist
        return array_merge($this->getDefaultPlanLimits(), $storedLimits);
    }

    /**
     * Get default plan limits
     */
    private function getDefaultPlanLimits(): array
    {
        return match ($this->plan) {
            'free' => [
                'monthly_sessions' => 1000,
                'monthly_events' => 5000,
                'sites' => 1,
                'exports' => 0,
            ],
            'basic' => [
                'monthly_sessions' => 10000,
                'monthly_events' => 50000,
                'sites' => 3,
                'exports' => 10,
            ],
            'premium' => [
                'monthly_sessions' => 100000,
                'monthly_events' => 500000,
                'sites' => 10,
                'exports' => 100,
            ],
            'enterprise' => [
                'monthly_sessions' => -1, // unlimited
                'monthly_events' => -1,
                'sites' => -1,
                'exports' => -1,
            ],
            default => [
                'monthly_sessions' => 1000,
                'monthly_events' => 5000,
                'sites' => 1,
                'exports' => 0,
            ],
        };
    }
}
