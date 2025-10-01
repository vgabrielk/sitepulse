<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Metric extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'metric_type',
        'metric_name',
        'value',
        'date',
        'period',
        'dimensions',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'date' => 'date',
        'dimensions' => 'array',
    ];

    /**
     * Get the site that owns this metric
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Metric types constants
     */
    public const METRIC_TYPES = [
        'visits' => 'visits',
        'sessions' => 'sessions',
        'events' => 'events',
        'reviews' => 'reviews',
        'bounce_rate' => 'bounce_rate',
        'avg_session_duration' => 'avg_session_duration',
        'page_views' => 'page_views',
        'unique_visitors' => 'unique_visitors',
    ];

    /**
     * Period constants
     */
    public const PERIOD_DAILY = 'daily';
    public const PERIOD_WEEKLY = 'weekly';
    public const PERIOD_MONTHLY = 'monthly';

    /**
     * Check if metric type is valid
     */
    public static function isValidMetricType(string $metricType): bool
    {
        return in_array($metricType, array_values(self::METRIC_TYPES));
    }

    /**
     * Check if period is valid
     */
    public static function isValidPeriod(string $period): bool
    {
        return in_array($period, [self::PERIOD_DAILY, self::PERIOD_WEEKLY, self::PERIOD_MONTHLY]);
    }

    /**
     * Get metrics for a specific site and date range
     */
    public static function getMetricsForSite(
        int $siteId,
        string $startDate,
        string $endDate,
        string $period = self::PERIOD_DAILY
    ): \Illuminate\Database\Eloquent\Collection {
        return self::where('site_id', $siteId)
            ->where('period', $period)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
    }
}
