<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'page_id',
        'url',
        'title',
        'visited_at',
        'time_on_page',
        'scroll_depth',
        'is_bounce',
        'is_exit',
        'page_data',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'is_bounce' => 'boolean',
        'is_exit' => 'boolean',
        'page_data' => 'array',
    ];

    /**
     * Get the session that owns this visit
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get the page that was visited
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get all events for this visit
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Mark as bounce
     */
    public function markAsBounce(): void
    {
        $this->update(['is_bounce' => true]);
    }

    /**
     * Mark as exit
     */
    public function markAsExit(): void
    {
        $this->update(['is_exit' => true]);
    }

    /**
     * Update time on page
     */
    public function updateTimeOnPage(int $seconds): void
    {
        $this->update(['time_on_page' => $seconds]);
    }

    /**
     * Update scroll depth
     */
    public function updateScrollDepth(int $percentage): void
    {
        $this->update(['scroll_depth' => $percentage]);
    }
}
