<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\RateLimitService;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    public function __construct(
        private RateLimitService $rateLimitService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $type = 'api'): Response
    {
        $rateLimitConfig = $this->rateLimitService->getRateLimitConfig();
        
        if (!isset($rateLimitConfig[$type])) {
            return $next($request);
        }

        $config = $rateLimitConfig[$type];
        $maxAttempts = $config['max_attempts'];
        $decayMinutes = $config['decay_minutes'];

        // Check rate limit based on type
        $allowed = match ($type) {
            'api' => $this->rateLimitService->checkApiRateLimit($request),
            'widget' => $this->rateLimitService->checkWidgetRateLimit($request, $request->route('siteId')),
            'login' => $this->rateLimitService->checkLoginRateLimit($request),
            'registration' => $this->rateLimitService->checkRegistrationRateLimit($request),
            'webhook' => $this->rateLimitService->checkWebhookRateLimit($request, $request->user()?->id),
            'export' => $this->rateLimitService->checkExportRateLimit($request, $request->user()?->id),
            default => true,
        };

        if (!$allowed) {
            $headers = $this->rateLimitService->getRateLimitHeaders($request, $type, $maxAttempts);
            
            return response()->json([
                'success' => false,
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $decayMinutes * 60,
            ], 429, $headers);
        }

        $response = $next($request);

        // Add rate limit headers to response
        $headers = $this->rateLimitService->getRateLimitHeaders($request, $type, $maxAttempts);
        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}
