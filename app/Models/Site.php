<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
        'widget_customization',
        'faq_customization',
        'is_active',
        'anonymize_ips',
        'track_events',
        'collect_feedback',
    ];

    protected $casts = [
        'widget_config' => 'array',
        'tracking_config' => 'array',
        'widget_customization' => 'array',
        'faq_customization' => 'array',
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
    public function visits(): HasManyThrough
    {
        return $this->hasManyThrough(Visit::class, Session::class);
    }

    /**
     * Get all events for this site (through sessions)
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
            '<!-- SitePulse Widget -->
<script async src="%s"></script>
<!-- End SitePulse Widget -->',
            $widgetUrl
        );
    }

    /**
     * Get review embed code
     */
    public function getReviewEmbedCode(): string
    {
        $customization = $this->widget_customization ?? $this->getDefaultCustomization();
        
        return sprintf(
            '<!-- SitePulse Reviews -->
<div id="sitepulse-reviews" data-widget-id="%s" data-api-url="%s" data-customization="%s"></div>
<script async src="%s/widget.js"></script>
<!-- End SitePulse Reviews -->',
            $this->widget_id,
            config('app.url'),
            htmlspecialchars(json_encode($customization)),
            config('app.url')
        );
    }

    /**
     * Generate custom CSS styles based on customization settings
     */
    public function generateCustomStyles(array $customization): string
    {
        $styles = [];
        
        // Colors
        $primaryColor = $customization['colors']['primary'] ?? '#007bff';
        $secondaryColor = $customization['colors']['secondary'] ?? '#6c757d';
        $backgroundColor = $customization['colors']['background'] ?? '#ffffff';
        $textColor = $customization['colors']['text'] ?? '#333333';
        $accentColor = $customization['colors']['accent'] ?? '#f39c12';
        
        // Typography
        $fontFamily = $customization['typography']['font_family'] ?? 'inherit';
        $fontSize = $customization['typography']['font_size'] ?? '14px';
        $fontWeight = $customization['typography']['font_weight'] ?? 'normal';
        
        // Layout
        $borderRadius = $customization['layout']['border_radius'] ?? '12px';
        $padding = $customization['layout']['padding'] ?? '20px';
        $margin = $customization['layout']['margin'] ?? '10px 0';
        $maxWidth = $customization['layout']['max_width'] ?? '800px';
        
        // Shadows
        $boxShadow = $customization['effects']['box_shadow'] ?? '0 4px 12px rgba(0,0,0,0.1)';
        $hoverShadow = $customization['effects']['hover_shadow'] ?? '0 6px 20px rgba(0,0,0,0.15)';
        
        // Animations
        $animation = $customization['effects']['animation'] ?? 'fadeIn 0.3s ease';
        
        $styles[] = '<style>';
        $styles[] = '#sitepulse-reviews {';
        $styles[] = '  font-family: ' . $fontFamily . ';';
        $styles[] = '  font-size: ' . $fontSize . ';';
        $styles[] = '  font-weight: ' . $fontWeight . ';';
        $styles[] = '  max-width: ' . $maxWidth . ';';
        $styles[] = '  margin: ' . $margin . ';';
        $styles[] = '  animation: ' . $animation . ';';
        $styles[] = '}';
        
        $styles[] = '#sitepulse-reviews .reviews-container {';
        $styles[] = '  background: ' . $backgroundColor . ';';
        $styles[] = '  border-radius: ' . $borderRadius . ';';
        $styles[] = '  padding: ' . $padding . ';';
        $styles[] = '  box-shadow: ' . $boxShadow . ';';
        $styles[] = '}';
        
        $styles[] = '#sitepulse-reviews .review-item {';
        $styles[] = '  background: ' . $backgroundColor . ';';
        $styles[] = '  border-radius: ' . $borderRadius . ';';
        $styles[] = '  box-shadow: ' . $boxShadow . ';';
        $styles[] = '  border-left-color: ' . $primaryColor . ';';
        $styles[] = '}';
        
        $styles[] = '#sitepulse-reviews .review-item:hover {';
        $styles[] = '  box-shadow: ' . $hoverShadow . ';';
        $styles[] = '}';
        
        $styles[] = '#sitepulse-reviews .review-form-toggle,';
        $styles[] = '#sitepulse-reviews .submit-btn {';
        $styles[] = '  background: ' . $primaryColor . ';';
        $styles[] = '  color: white;';
        $styles[] = '}';
        
        $styles[] = '#sitepulse-reviews .star-label.active {';
        $styles[] = '  color: ' . $accentColor . ';';
        $styles[] = '}';
        
        $styles[] = '#sitepulse-reviews .reviews-title {';
        $styles[] = '  color: ' . $textColor . ';';
        $styles[] = '}';
        
        $styles[] = '@keyframes fadeIn {';
        $styles[] = '  from { opacity: 0; transform: translateY(10px); }';
        $styles[] = '  to { opacity: 1; transform: translateY(0); }';
        $styles[] = '}';
        
        $styles[] = '</style>';
        
        return implode("\n", $styles);
    }

    /**
     * Get default customization settings
     */
    public function getDefaultCustomization(): array
    {
        return [
            'colors' => [
                'primary' => '#007bff',
                'secondary' => '#6c757d',
                'background' => '#ffffff',
                'text' => '#333333',
                'accent' => '#ffc107',
            ],
            'typography' => [
                'font_family' => 'inherit',
                'font_size' => '14px',
                'font_weight' => 'normal',
            ],
            'layout' => [
                'border_radius' => '8px',
                'padding' => '16px',
                'margin' => '10px 0',
                'max_width' => '600px',
            ],
            'effects' => [
                'box_shadow' => '0 2px 8px rgba(0,0,0,0.1)',
                'hover_shadow' => '0 4px 12px rgba(0,0,0,0.15)',
                'animation' => 'fadeIn 0.3s ease',
            ],
        ];
    }
}
