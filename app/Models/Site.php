<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'domain',
        'widget_id',
        'widget_config',
        'tracking_config',
        'is_active',
        'anonymize_ips',
        'track_events',
        'collect_feedback',
    ];

    protected $casts = [
        'widget_config' => 'array',
        'tracking_config' => 'array',
        'is_active' => 'boolean',
        'anonymize_ips' => 'boolean',
        'track_events' => 'boolean',
        'collect_feedback' => 'boolean',
    ];

    /**
     * Get the client that owns this site
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get all sessions for this site
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    /**
     * Get all visits for this site (through sessions)
     */
    public function visits()
    {
        return $this->hasManyThrough(Visit::class, Session::class, 'site_id', 'session_id');
    }

    /**
     * Get all events for this site (through visits)
     */
    public function events()
    {
        return Event::whereHas('visit.session', function ($query) {
            $query->where('site_id', $this->id);
        });
    }

    /**
     * Get all pages for this site
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Get all reviews for this site
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get all surveys for this site
     */
    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }

    /**
     * Get all metrics for this site
     */
    public function metrics(): HasMany
    {
        return $this->hasMany(Metric::class);
    }

    /**
     * Generate unique widget ID
     */
    public static function generateWidgetId(): string
    {
        do {
            $widgetId = 'sp_' . bin2hex(random_bytes(16));
        } while (self::where('widget_id', $widgetId)->exists());

        return $widgetId;
    }

    /**
     * Get widget embed code
     */
    public function getWidgetEmbedCode(): string
    {
        $widgetUrl = config('app.url') . '/widget/' . $this->widget_id . '.js';
        
        return sprintf(
            '<!-- SitePulse Analytics -->
<script async src="%s"></script>
<!-- End SitePulse Analytics -->',
            $widgetUrl
        );
    }
}
