<?php

namespace App\Repositories;

use App\Models\Event;
use App\DTOs\EventDTO;
use Illuminate\Database\Eloquent\Collection;

class EventRepository
{
    public function findById(int $id): ?Event
    {
        return Event::find($id);
    }

    public function getByVisitId(int $visitId): Collection
    {
        return Event::where('visit_id', $visitId)
            ->orderBy('occurred_at', 'asc')
            ->get();
    }

    public function getBySiteId(int $siteId, int $limit = 50, int $offset = 0): Collection
    {
        return Event::whereHas('visit.session', function ($query) use ($siteId) {
            $query->where('site_id', $siteId);
        })
        ->orderBy('occurred_at', 'desc')
        ->limit($limit)
        ->offset($offset)
        ->get();
    }

    public function getByDateRange(int $siteId, string $startDate, string $endDate): Collection
    {
        return Event::whereHas('visit.session', function ($query) use ($siteId) {
            $query->where('site_id', $siteId);
        })
        ->whereBetween('occurred_at', [$startDate, $endDate])
        ->orderBy('occurred_at', 'desc')
        ->get();
    }

    public function getByEventType(int $siteId, string $eventType, string $startDate, string $endDate): Collection
    {
        return Event::whereHas('visit.session', function ($query) use ($siteId) {
            $query->where('site_id', $siteId);
        })
        ->where('event_type', $eventType)
        ->whereBetween('occurred_at', [$startDate, $endDate])
        ->orderBy('occurred_at', 'desc')
        ->get();
    }

    public function create(array $data): Event
    {
        return Event::create($data);
    }

    public function update(Event $event, array $data): bool
    {
        return $event->update($data);
    }

    public function delete(Event $event): bool
    {
        return $event->delete();
    }

    public function getEventStats(int $siteId, string $startDate, string $endDate): array
    {
        $totalEvents = Event::whereHas('visit.session', function ($query) use ($siteId) {
            $query->where('site_id', $siteId);
        })
        ->whereBetween('occurred_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
        ->count();

        $eventTypes = Event::whereHas('visit.session', function ($query) use ($siteId) {
            $query->where('site_id', $siteId);
        })
        ->whereBetween('occurred_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
        ->selectRaw('event_type, COUNT(*) as count')
        ->groupBy('event_type')
        ->orderBy('count', 'desc')
        ->get();

        return [
            'total_events' => $totalEvents,
            'event_types' => $eventTypes,
        ];
    }

    public function getTopClickedElements(int $siteId, string $startDate, string $endDate, int $limit = 10): Collection
    {
        return Event::whereHas('visit.session', function ($query) use ($siteId) {
            $query->where('site_id', $siteId);
        })
        ->where('event_type', 'click')
        ->whereBetween('occurred_at', [$startDate, $endDate])
        ->whereNotNull('element_selector')
        ->selectRaw('element_selector, element_text, COUNT(*) as click_count')
        ->groupBy('element_selector', 'element_text')
        ->orderBy('click_count', 'desc')
        ->limit($limit)
        ->get();
    }

    public function getScrollStats(int $siteId, string $startDate, string $endDate): array
    {
        $scrollEvents = Event::whereHas('visit.session', function ($query) use ($siteId) {
            $query->where('site_id', $siteId);
        })
        ->where('event_type', 'scroll')
        ->whereBetween('occurred_at', [$startDate, $endDate])
        ->get();

        $avgScrollDepth = $scrollEvents->avg('event_data.scroll_depth');
        $maxScrollDepth = $scrollEvents->max('event_data.scroll_depth');

        return [
            'total_scroll_events' => $scrollEvents->count(),
            'avg_scroll_depth' => round($avgScrollDepth ?? 0, 2),
            'max_scroll_depth' => $maxScrollDepth ?? 0,
        ];
    }

    public function getFormInteractionStats(int $siteId, string $startDate, string $endDate): array
    {
        $formEvents = Event::whereHas('visit.session', function ($query) use ($siteId) {
            $query->where('site_id', $siteId);
        })
        ->whereIn('event_type', ['form_focus', 'form_blur', 'form_submit'])
        ->whereBetween('occurred_at', [$startDate, $endDate])
        ->get();

        $focusEvents = $formEvents->where('event_type', 'form_focus')->count();
        $blurEvents = $formEvents->where('event_type', 'form_blur')->count();
        $submitEvents = $formEvents->where('event_type', 'form_submit')->count();

        return [
            'form_focus_events' => $focusEvents,
            'form_blur_events' => $blurEvents,
            'form_submit_events' => $submitEvents,
            'total_form_events' => $formEvents->count(),
        ];
    }

    public function getHeatmapData(int $siteId, string $startDate, string $endDate): Collection
    {
        return Event::whereHas('visit.session', function ($query) use ($siteId) {
            $query->where('site_id', $siteId);
        })
        ->whereIn('event_type', ['click', 'scroll'])
        ->whereBetween('occurred_at', [$startDate, $endDate])
        ->whereNotNull('coordinates')
        ->selectRaw('coordinates, event_type, COUNT(*) as count')
        ->groupBy('coordinates', 'event_type')
        ->get();
    }
}
