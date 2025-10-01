<?php

namespace App\Repositories;

use App\Models\Session;
use App\DTOs\SessionDTO;
use Illuminate\Database\Eloquent\Collection;

class SessionRepository
{
    public function findById(int $id): ?Session
    {
        return Session::find($id);
    }

    public function findByToken(string $token): ?Session
    {
        return Session::where('session_token', $token)->first();
    }

    public function getBySiteId(int $siteId, int $limit = 50, int $offset = 0): Collection
    {
        return Session::where('site_id', $siteId)
            ->orderBy('started_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    public function getActiveBySiteId(int $siteId): Collection
    {
        return Session::where('site_id', $siteId)
            ->whereNull('ended_at')
            ->where('last_activity_at', '>', now()->subMinutes(30))
            ->get();
    }

    public function getByDateRange(int $siteId, string $startDate, string $endDate): Collection
    {
        return Session::where('site_id', $siteId)
            ->whereBetween('started_at', [$startDate, $endDate])
            ->orderBy('started_at', 'desc')
            ->get();
    }

    public function create(array $data): Session
    {
        return Session::create($data);
    }

    public function update(Session $session, array $data): bool
    {
        return $session->update($data);
    }

    public function delete(Session $session): bool
    {
        return $session->delete();
    }

    public function endSession(Session $session): bool
    {
        return $session->endSession();
    }

    public function updateActivity(Session $session): bool
    {
        return $session->updateActivity();
    }

    public function generateSessionToken(): string
    {
        return Session::generateSessionToken();
    }

    public function getSessionStats(int $siteId, string $startDate, string $endDate): array
    {
        $totalSessions = Session::where('site_id', $siteId)
            ->whereBetween('started_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();

        $activeSessions = Session::where('site_id', $siteId)
            ->whereNull('ended_at')
            ->where('last_activity_at', '>', now()->subMinutes(30))
            ->count();

        $avgDuration = Session::where('site_id', $siteId)
            ->whereBetween('started_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds');

        $bounceRate = Session::where('site_id', $siteId)
            ->whereBetween('started_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereHas('visits', function ($query) {
                $query->where('is_bounce', true);
            })
            ->count() / max($totalSessions, 1) * 100;

        return [
            'total_sessions' => $totalSessions,
            'active_sessions' => $activeSessions,
            'avg_duration' => round($avgDuration ?? 0, 2),
            'bounce_rate' => round($bounceRate, 2),
        ];
    }

    public function getTopCountries(int $siteId, string $startDate, string $endDate, int $limit = 10): Collection
    {
        return Session::where('site_id', $siteId)
            ->whereBetween('started_at', [$startDate, $endDate])
            ->whereNotNull('country')
            ->selectRaw('country, COUNT(*) as sessions_count')
            ->groupBy('country')
            ->orderBy('sessions_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTopReferrers(int $siteId, string $startDate, string $endDate, int $limit = 10): Collection
    {
        return Session::where('site_id', $siteId)
            ->whereBetween('started_at', [$startDate, $endDate])
            ->whereNotNull('referrer')
            ->selectRaw('referrer, COUNT(*) as sessions_count')
            ->groupBy('referrer')
            ->orderBy('sessions_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getDeviceStats(int $siteId, string $startDate, string $endDate): array
    {
        $sessions = Session::where('site_id', $siteId)
            ->whereBetween('started_at', [$startDate, $endDate])
            ->get();

        $deviceTypes = $sessions->groupBy('device_info.device_type')->map->count();
        $browsers = $sessions->groupBy('device_info.browser')->map->count();
        $operatingSystems = $sessions->groupBy('device_info.os')->map->count();

        return [
            'device_types' => $deviceTypes,
            'browsers' => $browsers,
            'operating_systems' => $operatingSystems,
        ];
    }
}
