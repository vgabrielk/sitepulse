<?php

namespace App\Repositories;

use App\Models\Site;
use App\DTOs\SiteDTO;
use Illuminate\Database\Eloquent\Collection;

class SiteRepository
{
    public function findById(int $id): ?Site
    {
        return Site::find($id);
    }

    public function findByWidgetId(string $widgetId): ?Site
    {
        return Site::where('widget_id', $widgetId)->first();
    }

    public function findByDomain(string $domain): ?Site
    {
        return Site::where('domain', $domain)->first();
    }

    public function getByClientId(int $clientId): Collection
    {
        return Site::where('client_id', $clientId)->get();
    }

    public function getActiveByClientId(int $clientId): Collection
    {
        return Site::where('client_id', $clientId)
            ->where('is_active', true)
            ->get();
    }

    public function create(array $data): Site
    {
        return Site::create($data);
    }

    public function update(Site $site, array $data): bool
    {
        return $site->update($data);
    }

    public function delete(Site $site): bool
    {
        return $site->delete();
    }

    public function activate(Site $site): bool
    {
        return $site->update(['is_active' => true]);
    }

    public function deactivate(Site $site): bool
    {
        return $site->update(['is_active' => false]);
    }

    public function updateWidgetConfig(Site $site, array $config): bool
    {
        return $site->update(['widget_config' => $config]);
    }

    public function updateTrackingConfig(Site $site, array $config): bool
    {
        return $site->update(['tracking_config' => $config]);
    }

    public function generateWidgetId(): string
    {
        return Site::generateWidgetId();
    }

    public function getSiteStats(Site $site): array
    {
        $sessions = $site->sessions()->count();
        $visits = $site->visits()->count();
        $events = $site->events()->count();
        $reviews = $site->reviews()->count();
        $pages = $site->pages()->count();

        return [
            'sessions' => $sessions,
            'visits' => $visits,
            'events' => $events,
            'reviews' => $reviews,
            'pages' => $pages,
        ];
    }

    public function getSiteMetrics(Site $site, string $startDate, string $endDate): array
    {
        $sessions = $site->sessions()
            ->whereBetween('started_at', [$startDate, $endDate])
            ->count();

        $visits = $site->visits()
            ->whereBetween('visited_at', [$startDate, $endDate])
            ->count();

        $events = $site->events()
            ->whereBetween('occurred_at', [$startDate, $endDate])
            ->count();

        $reviews = $site->reviews()
            ->whereBetween('submitted_at', [$startDate, $endDate])
            ->count();

        $avgSessionDuration = $site->sessions()
            ->whereBetween('started_at', [$startDate, $endDate])
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds');

        $bounceRate = $site->visits()
            ->whereBetween('visited_at', [$startDate, $endDate])
            ->where('is_bounce', true)
            ->count() / max($visits, 1) * 100;

        return [
            'sessions' => $sessions,
            'visits' => $visits,
            'events' => $events,
            'reviews' => $reviews,
            'avg_session_duration' => round($avgSessionDuration ?? 0, 2),
            'bounce_rate' => round($bounceRate, 2),
        ];
    }
}
