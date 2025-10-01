<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'url',
        'title',
        'path',
        'query_string',
        'hash',
        'views_count',
        'unique_views_count',
        'avg_time_on_page',
        'bounce_rate',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'unique_views_count' => 'integer',
        'avg_time_on_page' => 'decimal:2',
        'bounce_rate' => 'decimal:2',
    ];

    /**
     * Get the site that owns this page
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get all visits for this page
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * Increment views count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment unique views count
     */
    public function incrementUniqueViews(): void
    {
        $this->increment('unique_views_count');
    }

    /**
     * Update average time on page
     */
    public function updateAvgTimeOnPage(int $timeOnPage): void
    {
        $totalTime = $this->avg_time_on_page * $this->views_count;
        $newAvgTime = ($totalTime + $timeOnPage) / ($this->views_count + 1);
        
        $this->update(['avg_time_on_page' => round($newAvgTime, 2)]);
    }

    /**
     * Update bounce rate
     */
    public function updateBounceRate(): void
    {
        $bounces = $this->visits()->where('is_bounce', true)->count();
        $totalVisits = $this->visits()->count();
        
        if ($totalVisits > 0) {
            $bounceRate = ($bounces / $totalVisits) * 100;
            $this->update(['bounce_rate' => round($bounceRate, 2)]);
        }
    }
}
