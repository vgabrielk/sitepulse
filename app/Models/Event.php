<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'event_type',
        'element_selector',
        'element_text',
        'element_tag',
        'coordinates',
        'event_data',
        'occurred_at',
    ];

    protected $casts = [
        'coordinates' => 'array',
        'event_data' => 'array',
        'occurred_at' => 'datetime',
    ];

    /**
     * Get the visit that owns this event
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get the session through the visit
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id', 'id')
            ->through('visit');
    }

    /**
     * Get the site through the visit and session
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id', 'id')
            ->through(['visit.session']);
    }

    /**
     * Event types constants
     */
    public const EVENT_TYPES = [
        'click' => 'click',
        'scroll' => 'scroll',
        'form_submit' => 'form_submit',
        'form_focus' => 'form_focus',
        'form_blur' => 'form_blur',
        'page_view' => 'page_view',
        'page_exit' => 'page_exit',
        'button_click' => 'button_click',
        'link_click' => 'link_click',
        'video_play' => 'video_play',
        'video_pause' => 'video_pause',
        'download' => 'download',
        'search' => 'search',
        'custom' => 'custom',
    ];

    /**
     * Check if event type is valid
     */
    public static function isValidEventType(string $eventType): bool
    {
        return in_array($eventType, array_values(self::EVENT_TYPES));
    }
}
