<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService
    ) {}

    public function getOverview(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            $sessionStats = $this->analyticsService->getSessionStats($site, $startDate, $endDate);
            $eventStats = $this->analyticsService->getEventStats($site, $startDate, $endDate);
            $metrics = $this->analyticsService->getMetrics($site, $startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'sessions' => $sessionStats,
                    'events' => $eventStats,
                    'metrics' => $metrics->toArray(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get analytics overview',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSessions(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'limit' => 'sometimes|integer|min:1|max:100',
            'offset' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            $limit = $request->get('limit', 50);
            $offset = $request->get('offset', 0);
            
            $sessions = $this->analyticsService->getSessionsByDateRange($site, $startDate, $endDate, $limit, $offset);
            
            return response()->json([
                'success' => true,
                'data' => $sessions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get sessions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getEvents(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'event_type' => 'sometimes|string',
            'limit' => 'sometimes|integer|min:1|max:100',
            'offset' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            $eventType = $request->get('event_type');
            $limit = $request->get('limit', 50);
            $offset = $request->get('offset', 0);
            
            if ($eventType) {
                $events = $this->analyticsService->getEventsByType($site, $eventType, $startDate, $endDate, $limit, $offset);
            } else {
                $events = $this->analyticsService->getEventsByDateRange($site, $startDate, $endDate, $limit, $offset);
            }
            
            return response()->json([
                'success' => true,
                'data' => $events,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get events',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTopPages(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'limit' => 'sometimes|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            $limit = $request->get('limit', 10);
            
            $topPages = $this->analyticsService->getTopPages($site, $startDate, $endDate, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $topPages,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get top pages',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTopEvents(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'limit' => 'sometimes|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            $limit = $request->get('limit', 10);
            
            $topEvents = $this->analyticsService->getTopEvents($site, $startDate, $endDate, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $topEvents,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get top events',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getHeatmapData(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            $heatmapData = $this->analyticsService->getHeatmapData($site, $startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => $heatmapData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get heatmap data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getRealTimeMetrics(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $realTimeMetrics = $this->analyticsService->getRealTimeMetrics($site);
            
            return response()->json([
                'success' => true,
                'data' => $realTimeMetrics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get real-time metrics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTrendData(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'metric_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'period' => 'sometimes|string|in:daily,weekly,monthly',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $trendData = $this->analyticsService->getTrendData(
                $site,
                $request->metric_type,
                $request->start_date,
                $request->end_date,
                $request->get('period', 'daily')
            );
            
            return response()->json([
                'success' => true,
                'data' => $trendData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get trend data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
