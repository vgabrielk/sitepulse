<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->plan_limits ?? $this->getDefaultPlanLimits();
    }

    /**
     * Get default plan limits
     */
    private function getDefaultPlanLimits(): array
    {
        return match ($this->plan) {
            'free' => [
                'monthly_visits' => 1000,
                'monthly_events' => 5000,
                'sites' => 1,
                'reviews' => 50,
                'exports' => 0,
            ],
            'basic' => [
                'monthly_visits' => 10000,
                'monthly_events' => 50000,
                'sites' => 3,
                'reviews' => 500,
                'exports' => 10,
            ],
            'premium' => [
                'monthly_visits' => 100000,
                'monthly_events' => 500000,
                'sites' => 10,
                'reviews' => 5000,
                'exports' => 100,
            ],
            'enterprise' => [
                'monthly_visits' => -1, // unlimited
                'monthly_events' => -1,
                'sites' => -1,
                'reviews' => -1,
                'exports' => -1,
            ],
            default => [
                'monthly_visits' => 1000,
                'monthly_events' => 5000,
                'sites' => 1,
                'reviews' => 50,
                'exports' => 0,
            ],
        };
    }
}
