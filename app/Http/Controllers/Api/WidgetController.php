<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WidgetService;
use App\Services\AnalyticsService;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class WidgetController extends Controller
{
    public function __construct(
        private WidgetService $widgetService,
        private AnalyticsService $analyticsService
    ) {}

    public function getScript(string $widgetId): string
    {
        try {
            $site = Site::where('widget_id', $widgetId)->first();
            
            if (!$site || !$site->is_active) {
                return '// SitePulse: Site not found or inactive';
            }
            
            return $this->widgetService->generateWidgetScript($site);
        } catch (\Exception $e) {
            return '// SitePulse: Error loading script - ' . $e->getMessage();
        }
    }

    public function trackEvents(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|integer|exists:sites,id',
            'events' => 'required|array',
            'events.*.type' => 'required|string',
            'events.*.data' => 'sometimes|array',
            'events.*.timestamp' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::find($request->site_id);
            
            if (!$site || !$site->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found or inactive',
                ], 404);
            }

            $events = $request->events;
            $processedEvents = 0;

            foreach ($events as $eventData) {
                // Find or create session for this IP
                $session = $this->findOrCreateSessionForIP($site, $request);
                
                // Find or create visit for this URL
                $visit = $this->findOrCreateVisitForURL($session, $eventData);
                
                // For pageview events, check if we already have a recent pageview for this URL
                if ($eventData['type'] === 'pageview') {
                    $recentPageview = \App\Models\Event::where('visit_id', $visit->id)
                        ->where('event_type', 'pageview')
                        ->where('occurred_at', '>', now()->subMinutes(5))
                        ->first();
                    
                    if ($recentPageview) {
                        // Skip creating duplicate pageview event
                        continue;
                    }
                }
                
                // Create event
                $event = \App\Models\Event::create([
                    'visit_id' => $visit->id,
                    'event_type' => $eventData['type'],
                    'element_selector' => $eventData['data']['selector'] ?? null,
                    'element_text' => $eventData['data']['text'] ?? null,
                    'element_tag' => $eventData['data']['element'] ?? null,
                    'coordinates' => $eventData['data']['coordinates'] ?? null,
                    'event_data' => $eventData['data'],
                    'occurred_at' => now(),
                ]);
                
                $processedEvents++;
            }

            return response()->json([
                'success' => true,
                'message' => 'Events tracked successfully',
                'data' => [
                    'processed_events' => $processedEvents,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in trackEvents: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to track events',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function submitReview(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|integer|exists:sites,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'sometimes|string|max:1000',
            'visitor_name' => 'sometimes|string|max:255',
            'visitor_email' => 'sometimes|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::find($request->site_id);
            
            if (!$site || !$site->is_active || !$site->collect_feedback) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found, inactive, or feedback collection disabled',
                ], 404);
            }

            // Find or create session
            $session = $this->findOrCreateSession($site, $request);
            
            // Create review
            $reviewData = $request->only(['rating', 'comment', 'visitor_name', 'visitor_email']);
            $reviewData['ip_address'] = $request->ip();
            
            $review = $this->analyticsService->createReview($site, $reviewData, $session);
            
            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully',
                'data' => $review->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getReviews(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|integer|exists:sites,id',
            'limit' => 'sometimes|integer|min:1|max:50',
            'rating' => 'sometimes|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::find($request->site_id);
            
            if (!$site || !$site->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found or inactive',
                ], 404);
            }

            $limit = $request->get('limit', 10);
            $rating = $request->get('rating');
            
            if ($rating) {
                $reviews = $this->analyticsService->getReviewsByRating($site, $rating, $limit);
            } else {
                $reviews = $this->analyticsService->getApprovedReviews($site, $limit);
            }
            
            return response()->json([
                'success' => true,
                'data' => $reviews,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get reviews',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getConfig(Request $request, string $widgetId): JsonResponse
    {
        try {
            $site = Site::where('widget_id', $widgetId)->first();
            
            if (!$site || !$site->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found or inactive',
                ], 404);
            }
            
            $config = $this->widgetService->getWidgetConfig($site);
            
            return response()->json([
                'success' => true,
                'data' => $config,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get widget config',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function findOrCreateSessionForIP(Site $site, Request $request): \App\Models\Session
    {
        $ipAddress = $request->ip();
        
        // Look for active session from same IP in last 30 minutes
        $session = \App\Models\Session::where('site_id', $site->id)
            ->where('ip_address', $ipAddress)
            ->where('last_activity_at', '>', now()->subMinutes(30))
            ->whereNull('ended_at')
            ->first();
        
        if ($session) {
            // Update activity
            $session->update(['last_activity_at' => now()]);
            return $session;
        }
        
        // Create new session
        return \App\Models\Session::create([
            'site_id' => $site->id,
            'session_token' => 'sp_' . time() . '_' . rand(1000, 9999),
            'ip_address' => $ipAddress,
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('Referer'),
            'country' => null,
            'device_info' => [],
            'started_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    private function findOrCreateVisitForURL(\App\Models\Session $session, array $eventData): \App\Models\Visit
    {
        $url = $eventData['data']['url'] ?? '/';
        $title = $eventData['data']['title'] ?? '';
        
        // Look for recent visit to same URL in last 30 minutes (increased from 5 minutes)
        $visit = \App\Models\Visit::where('session_id', $session->id)
            ->where('url', $url)
            ->where('visited_at', '>', now()->subMinutes(30))
            ->first();
        
        if ($visit) {
            // Update visit timestamp and title if changed
            $visit->update([
                'visited_at' => now(),
                'title' => $title
            ]);
            return $visit;
        }
        
        // Find or create page for this URL
        $page = \App\Models\Page::firstOrCreate([
            'site_id' => $session->site_id,
            'url' => $url,
        ], [
            'title' => $title,
            'path' => parse_url($url, PHP_URL_PATH) ?? '/',
            'query_string' => parse_url($url, PHP_URL_QUERY),
            'hash' => parse_url($url, PHP_URL_FRAGMENT),
        ]);
        
        // Create new visit
        return \App\Models\Visit::create([
            'session_id' => $session->id,
            'page_id' => $page->id,
            'url' => $url,
            'title' => $title,
            'visited_at' => now(),
        ]);
    }

    private function findOrCreateSession(Site $site, Request $request): \App\Models\Session
    {
        $sessionToken = $request->header('X-Session-Token');
        
        if ($sessionToken) {
            $session = \App\Models\Session::where('session_token', $sessionToken)
                ->where('site_id', $site->id)
                ->first();
            
            if ($session && $session->isActive()) {
                $this->analyticsService->updateSessionActivity($session);
                return $session;
            }
        }
        
        // Create new session
        $sessionData = [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('Referer'),
            'country' => $this->getCountryFromIP($request->ip()),
            'device_info' => $this->getDeviceInfo($request->userAgent()),
        ];
        
        return $this->analyticsService->createSession($site, $sessionData);
    }

    private function findOrCreateVisit(\App\Models\Session $session, array $eventData): \App\Models\Visit
    {
        $url = $eventData['data']['url'] ?? $eventData['data']['page'] ?? '/';
        $title = $eventData['data']['title'] ?? '';
        
        // Find existing visit for this session and URL
        $visit = \App\Models\Visit::where('session_id', $session->id)
            ->where('url', $url)
            ->first();
        
        if ($visit) {
            return $visit;
        }
        
        // Create new visit
        $page = \App\Models\Page::firstOrCreate([
            'site_id' => $session->site_id,
            'url' => $url,
        ], [
            'title' => $title,
            'path' => parse_url($url, PHP_URL_PATH) ?? '/',
            'query_string' => parse_url($url, PHP_URL_QUERY),
            'hash' => parse_url($url, PHP_URL_FRAGMENT),
        ]);
        
        return $this->analyticsService->trackVisit($session, [
            'page_id' => $page->id,
            'url' => $url,
            'title' => $title,
        ]);
    }

    private function getCountryFromIP(string $ip): ?string
    {
        // Simple IP to country mapping (in production, use a proper service)
        // This is just a placeholder implementation
        return null;
    }

    private function getDeviceInfo(string $userAgent): array
    {
        // Simple user agent parsing (in production, use a proper library)
        $deviceInfo = [
            'browser' => 'Unknown',
            'os' => 'Unknown',
            'device_type' => 'desktop',
        ];
        
        if (strpos($userAgent, 'Mobile') !== false) {
            $deviceInfo['device_type'] = 'mobile';
        } elseif (strpos($userAgent, 'Tablet') !== false) {
            $deviceInfo['device_type'] = 'tablet';
        }
        
        if (strpos($userAgent, 'Chrome') !== false) {
            $deviceInfo['browser'] = 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $deviceInfo['browser'] = 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $deviceInfo['browser'] = 'Safari';
        }
        
        if (strpos($userAgent, 'Windows') !== false) {
            $deviceInfo['os'] = 'Windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            $deviceInfo['os'] = 'macOS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $deviceInfo['os'] = 'Linux';
        }
        
        return $deviceInfo;
    }
}
