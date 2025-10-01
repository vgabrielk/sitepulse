<?php

namespace App\Services;

use App\Models\Site;
use App\Models\Session;
use App\Models\Visit;
use App\Models\Event;
use App\Repositories\SessionRepository;
use App\Repositories\EventRepository;
use App\Repositories\MetricsRepository;
use App\DTOs\SessionDTO;
use App\DTOs\EventDTO;
use App\DTOs\MetricsDTO;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function __construct(
        private SessionRepository $sessionRepository,
        private EventRepository $eventRepository,
        private MetricsRepository $metricsRepository
    ) {}

    public function trackSession(Site $site, array $sessionData): SessionDTO
    {
        $sessionData['site_id'] = $site->id;
        $sessionData['session_token'] = $this->sessionRepository->generateSessionToken();
        $sessionData['started_at'] = now();
        $sessionData['last_activity_at'] = now();
        
        $session = $this->sessionRepository->create($sessionData);
        
        return SessionDTO::fromModel($session);
    }

    public function createSession(Site $site, array $sessionData): \App\Models\Session
    {
        $sessionData['site_id'] = $site->id;
        $sessionData['session_token'] = $this->sessionRepository->generateSessionToken();
        $sessionData['started_at'] = now();
        $sessionData['last_activity_at'] = now();
        
        return $this->sessionRepository->create($sessionData);
    }

    public function trackVisit(Session $session, array $visitData): Visit
    {
        $visitData['session_id'] = $session->id;
        $visitData['visited_at'] = now();
        
        $visit = Visit::create($visitData);
        
        // Update session activity
        $this->sessionRepository->updateActivity($session);
        
        return $visit;
    }

    public function trackEvent(Visit $visit, array $eventData): EventDTO
    {
        $eventData['visit_id'] = $visit->id;
        $eventData['occurred_at'] = now();
        
        $event = $this->eventRepository->create($eventData);
        
        return EventDTO::fromModel($event);
    }

    public function updateSessionActivity(Session $session): void
    {
        $this->sessionRepository->updateActivity($session);
    }

    public function endSession(Session $session): void
    {
        $this->sessionRepository->endSession($session);
    }

    public function getSessionStats(Site $site, string $startDate, string $endDate): array
    {
        return $this->sessionRepository->getSessionStats($site->id, $startDate, $endDate);
    }

    public function getEventStats(Site $site, string $startDate, string $endDate): array
    {
        return $this->eventRepository->getEventStats($site->id, $startDate, $endDate);
    }

    public function getTopClickedElements(Site $site, string $startDate, string $endDate, int $limit = 10): array
    {
        return $this->eventRepository->getTopClickedElements($site->id, $startDate, $endDate, $limit)->toArray();
    }

    public function getScrollStats(Site $site, string $startDate, string $endDate): array
    {
        return $this->eventRepository->getScrollStats($site->id, $startDate, $endDate);
    }

    public function getFormInteractionStats(Site $site, string $startDate, string $endDate): array
    {
        return $this->eventRepository->getFormInteractionStats($site->id, $startDate, $endDate);
    }

    public function getHeatmapData(Site $site, string $startDate, string $endDate): array
    {
        return $this->eventRepository->getHeatmapData($site->id, $startDate, $endDate)->toArray();
    }

    public function getTopCountries(Site $site, string $startDate, string $endDate, int $limit = 10): array
    {
        return $this->sessionRepository->getTopCountries($site->id, $startDate, $endDate, $limit)->toArray();
    }

    public function getTopReferrers(Site $site, string $startDate, string $endDate, int $limit = 10): array
    {
        return $this->sessionRepository->getTopReferrers($site->id, $startDate, $endDate, $limit)->toArray();
    }

    public function getDeviceStats(Site $site, string $startDate, string $endDate): array
    {
        return $this->sessionRepository->getDeviceStats($site->id, $startDate, $endDate);
    }

    public function getMetrics(Site $site, string $startDate, string $endDate, string $period = 'daily'): MetricsDTO
    {
        $metrics = $this->metricsRepository->getBySiteId($site->id, $startDate, $endDate, $period);
        
        $summary = $this->metricsRepository->getConversionMetrics($site->id, $startDate, $endDate);
        
        return MetricsDTO::create(
            siteId: $site->id,
            period: $period,
            startDate: $startDate,
            endDate: $endDate,
            metrics: $metrics->toArray(),
            summary: $summary
        );
    }

    public function getRealTimeMetrics(Site $site): array
    {
        return $this->metricsRepository->getRealTimeMetrics($site->id);
    }

    public function getSiteMetrics(Site $site, string $startDate, string $endDate): array
    {
        $sessionStats = $this->getSessionStats($site, $startDate, $endDate);
        $eventStats = $this->getEventStats($site, $startDate, $endDate);
        
        // Get visits count directly
        $visitsCount = \App\Models\Visit::whereHas('session', function ($query) use ($site) {
            $query->where('site_id', $site->id);
        })
        ->whereBetween('visited_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
        ->count();
        
        return [
            'sessions' => $sessionStats['total_sessions'] ?? 0,
            'visits' => $visitsCount,
            'events' => $eventStats['total_events'] ?? 0,
            'unique_visitors' => $sessionStats['unique_visitors'] ?? 0,
            'bounce_rate' => $sessionStats['bounce_rate'] ?? 0,
            'avg_session_duration' => $sessionStats['avg_duration'] ?? 0,
        ];
    }

    public function getTrendData(Site $site, string $metricType, string $startDate, string $endDate, string $period = 'daily'): array
    {
        return $this->metricsRepository->getTrendData($site->id, $metricType, $startDate, $endDate, $period);
    }

    public function createReview(Site $site, array $reviewData, \App\Models\Session $session): \App\Models\Review
    {
        $reviewData['site_id'] = $site->id;
        $reviewData['session_id'] = $session->id;
        $reviewData['status'] = 'pending';
        $reviewData['submitted_at'] = now();
        
        return \App\Models\Review::create($reviewData);
    }

    public function getReviewsByRating(Site $site, int $rating, int $limit = 10): array
    {
        return \App\Models\Review::where('site_id', $site->id)
            ->where('rating', $rating)
            ->where('status', 'approved')
            ->orderBy('submitted_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getApprovedReviews(Site $site, int $limit = 10): array
    {
        return \App\Models\Review::where('site_id', $site->id)
            ->where('status', 'approved')
            ->orderBy('submitted_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function aggregateMetrics(Site $site, string $date, string $period = 'daily'): void
    {
        $startOfPeriod = $this->getStartOfPeriod($date, $period);
        $endOfPeriod = $this->getEndOfPeriod($date, $period);
        
        // Aggregate visits
        $visits = Visit::whereHas('session', function ($query) use ($site) {
            $query->where('site_id', $site->id);
        })
        ->whereBetween('visited_at', [$startOfPeriod, $endOfPeriod])
        ->count();
        
        $this->metricsRepository->create([
            'site_id' => $site->id,
            'metric_type' => 'visits',
            'metric_name' => 'total_visits',
            'value' => $visits,
            'date' => $date,
            'period' => $period,
        ]);
        
        // Aggregate sessions
        $sessions = Session::where('site_id', $site->id)
            ->whereBetween('started_at', [$startOfPeriod, $endOfPeriod])
            ->count();
        
        $this->metricsRepository->create([
            'site_id' => $site->id,
            'metric_type' => 'sessions',
            'metric_name' => 'total_sessions',
            'value' => $sessions,
            'date' => $date,
            'period' => $period,
        ]);
        
        // Aggregate events
        $events = Event::whereHas('visit.session', function ($query) use ($site) {
            $query->where('site_id', $site->id);
        })
        ->whereBetween('occurred_at', [$startOfPeriod, $endOfPeriod])
        ->count();
        
        $this->metricsRepository->create([
            'site_id' => $site->id,
            'metric_type' => 'events',
            'metric_name' => 'total_events',
            'value' => $events,
            'date' => $date,
            'period' => $period,
        ]);
    }

    private function getStartOfPeriod(string $date, string $period): string
    {
        $dateObj = \Carbon\Carbon::parse($date);
        
        return match ($period) {
            'daily' => $dateObj->startOfDay()->toISOString(),
            'weekly' => $dateObj->startOfWeek()->toISOString(),
            'monthly' => $dateObj->startOfMonth()->toISOString(),
            default => $dateObj->startOfDay()->toISOString(),
        };
    }

    private function getEndOfPeriod(string $date, string $period): string
    {
        $dateObj = \Carbon\Carbon::parse($date);
        
        return match ($period) {
            'daily' => $dateObj->endOfDay()->toISOString(),
            'weekly' => $dateObj->endOfWeek()->toISOString(),
            'monthly' => $dateObj->endOfMonth()->toISOString(),
            default => $dateObj->endOfDay()->toISOString(),
        };
    }
}
