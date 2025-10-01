<?php

namespace App\Repositories;

use App\Models\Metric;
use App\DTOs\MetricsDTO;
use Illuminate\Database\Eloquent\Collection;

class MetricsRepository
{
    public function findById(int $id): ?Metric
    {
        return Metric::find($id);
    }

    public function getBySiteId(int $siteId, string $startDate, string $endDate, string $period = 'daily'): Collection
    {
        return Metric::where('site_id', $siteId)
            ->where('period', $period)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
    }

    public function getByMetricType(int $siteId, string $metricType, string $startDate, string $endDate, string $period = 'daily'): Collection
    {
        return Metric::where('site_id', $siteId)
            ->where('metric_type', $metricType)
            ->where('period', $period)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
    }

    public function create(array $data): Metric
    {
        return Metric::create($data);
    }

    public function update(Metric $metric, array $data): bool
    {
        return $metric->update($data);
    }

    public function delete(Metric $metric): bool
    {
        return $metric->delete();
    }

    public function getAggregatedMetrics(int $siteId, string $startDate, string $endDate, string $period = 'daily'): array
    {
        $metrics = $this->getBySiteId($siteId, $startDate, $endDate, $period);

        $aggregated = [];
        foreach ($metrics as $metric) {
            $key = $metric->metric_type . '_' . $metric->metric_name;
            if (!isset($aggregated[$key])) {
                $aggregated[$key] = [
                    'metric_type' => $metric->metric_type,
                    'metric_name' => $metric->metric_name,
                    'total_value' => 0,
                    'avg_value' => 0,
                    'data_points' => [],
                ];
            }
            $aggregated[$key]['total_value'] += $metric->value;
            $aggregated[$key]['data_points'][] = [
                'date' => $metric->date->format('Y-m-d'),
                'value' => $metric->value,
            ];
        }

        // Calculate averages
        foreach ($aggregated as &$metric) {
            $metric['avg_value'] = count($metric['data_points']) > 0 
                ? $metric['total_value'] / count($metric['data_points']) 
                : 0;
        }

        return $aggregated;
    }

    public function getTopPages(int $siteId, string $startDate, string $endDate, int $limit = 10): Collection
    {
        return Metric::where('site_id', $siteId)
            ->where('metric_type', 'page_views')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('dimensions->>"$.page_url" as page_url, SUM(value) as total_views')
            ->groupBy('dimensions->>"$.page_url"')
            ->orderBy('total_views', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTopEvents(int $siteId, string $startDate, string $endDate, int $limit = 10): Collection
    {
        return Metric::where('site_id', $siteId)
            ->where('metric_type', 'events')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('dimensions->>"$.event_type" as event_type, SUM(value) as total_events')
            ->groupBy('dimensions->>"$.event_type"')
            ->orderBy('total_events', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getConversionMetrics(int $siteId, string $startDate, string $endDate): array
    {
        $visits = Metric::where('site_id', $siteId)
            ->where('metric_type', 'visits')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('value');

        $sessions = Metric::where('site_id', $siteId)
            ->where('metric_type', 'sessions')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('value');

        $events = Metric::where('site_id', $siteId)
            ->where('metric_type', 'events')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('value');

        $reviews = Metric::where('site_id', $siteId)
            ->where('metric_type', 'reviews')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('value');

        return [
            'visits' => $visits,
            'sessions' => $sessions,
            'events' => $events,
            'reviews' => $reviews,
            'conversion_rate' => $sessions > 0 ? round(($reviews / $sessions) * 100, 2) : 0,
            'engagement_rate' => $visits > 0 ? round(($events / $visits) * 100, 2) : 0,
        ];
    }

    public function getTrendData(int $siteId, string $metricType, string $startDate, string $endDate, string $period = 'daily'): array
    {
        $metrics = $this->getByMetricType($siteId, $metricType, $startDate, $endDate, $period);

        $trendData = [];
        foreach ($metrics as $metric) {
            $trendData[] = [
                'date' => $metric->date->format('Y-m-d'),
                'value' => $metric->value,
                'metric_name' => $metric->metric_name,
            ];
        }

        return $trendData;
    }

    public function getRealTimeMetrics(int $siteId): array
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        $todayMetrics = $this->getBySiteId($siteId, $today, $today);
        $yesterdayMetrics = $this->getBySiteId($siteId, $yesterday, $yesterday);

        $todayData = [];
        foreach ($todayMetrics as $metric) {
            $key = $metric->metric_type . '_' . $metric->metric_name;
            $todayData[$key] = $metric->value;
        }

        $yesterdayData = [];
        foreach ($yesterdayMetrics as $metric) {
            $key = $metric->metric_type . '_' . $metric->metric_name;
            $yesterdayData[$key] = $metric->value;
        }

        return [
            'today' => $todayData,
            'yesterday' => $yesterdayData,
            'comparison' => $this->calculateComparison($todayData, $yesterdayData),
        ];
    }

    private function calculateComparison(array $today, array $yesterday): array
    {
        $comparison = [];
        foreach ($today as $key => $value) {
            $yesterdayValue = $yesterday[$key] ?? 0;
            $change = $yesterdayValue > 0 ? (($value - $yesterdayValue) / $yesterdayValue) * 100 : 0;
            $comparison[$key] = [
                'change_percent' => round($change, 2),
                'change_absolute' => $value - $yesterdayValue,
                'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable'),
            ];
        }
        return $comparison;
    }
}
