<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class RateLimitService
{
    public function checkRateLimit(Request $request, string $key, int $maxAttempts, int $decayMinutes = 1): bool
    {
        $key = $this->getRateLimitKey($request, $key);
        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            Log::warning('Rate limit exceeded', [
                'key' => $key,
                'attempts' => $attempts,
                'max_attempts' => $maxAttempts,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return false;
        }

        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));
        return true;
    }

    public function checkApiRateLimit(Request $request, string $apiKey = null): bool
    {
        $key = $apiKey ?: $request->ip();
        $maxAttempts = config('sitepulse.rate_limit_per_minute', 60);
        
        return $this->checkRateLimit($request, "api:{$key}", $maxAttempts, 1);
    }

    public function checkWidgetRateLimit(Request $request, string $siteId): bool
    {
        $key = $request->ip() . ":{$siteId}";
        $maxAttempts = 100; // Allow more requests for widget tracking
        
        return $this->checkRateLimit($request, "widget:{$key}", $maxAttempts, 1);
    }

    public function checkLoginRateLimit(Request $request): bool
    {
        $key = $request->ip();
        $maxAttempts = 5; // 5 login attempts per minute
        
        return $this->checkRateLimit($request, "login:{$key}", $maxAttempts, 1);
    }

    public function checkRegistrationRateLimit(Request $request): bool
    {
        $key = $request->ip();
        $maxAttempts = 3; // 3 registrations per hour
        
        return $this->checkRateLimit($request, "registration:{$key}", $maxAttempts, 60);
    }

    public function checkWebhookRateLimit(Request $request, string $clientId): bool
    {
        $key = $clientId;
        $maxAttempts = 100; // 100 webhook calls per minute
        
        return $this->checkRateLimit($request, "webhook:{$key}", $maxAttempts, 1);
    }

    public function checkExportRateLimit(Request $request, string $clientId): bool
    {
        $key = $clientId;
        $maxAttempts = 10; // 10 exports per hour
        
        return $this->checkRateLimit($request, "export:{$key}", $maxAttempts, 60);
    }

    public function getRateLimitInfo(Request $request, string $key): array
    {
        $rateLimitKey = $this->getRateLimitKey($request, $key);
        $attempts = Cache::get($rateLimitKey, 0);
        $expiresAt = Cache::get($rateLimitKey . ':expires', null);

        return [
            'attempts' => $attempts,
            'expires_at' => $expiresAt,
            'remaining_time' => $expiresAt ? now()->diffInSeconds($expiresAt) : 0,
        ];
    }

    public function resetRateLimit(Request $request, string $key): void
    {
        $rateLimitKey = $this->getRateLimitKey($request, $key);
        Cache::forget($rateLimitKey);
        Cache::forget($rateLimitKey . ':expires');
    }

    public function getRateLimitHeaders(Request $request, string $key, int $maxAttempts): array
    {
        $rateLimitKey = $this->getRateLimitKey($request, $key);
        $attempts = Cache::get($rateLimitKey, 0);
        $expiresAt = Cache::get($rateLimitKey . ':expires', null);

        $headers = [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => max(0, $maxAttempts - $attempts),
        ];

        if ($expiresAt) {
            $headers['X-RateLimit-Reset'] = $expiresAt->timestamp;
        }

        return $headers;
    }

    public function isBlocked(Request $request, string $key): bool
    {
        $rateLimitKey = $this->getRateLimitKey($request, $key);
        $attempts = Cache::get($rateLimitKey, 0);
        $expiresAt = Cache::get($rateLimitKey . ':expires', null);

        if ($expiresAt && now()->isAfter($expiresAt)) {
            // Rate limit has expired
            Cache::forget($rateLimitKey);
            Cache::forget($rateLimitKey . ':expires');
            return false;
        }

        return $attempts > 0;
    }

    public function getBlockedUntil(Request $request, string $key): ?\Carbon\Carbon
    {
        $rateLimitKey = $this->getRateLimitKey($request, $key);
        return Cache::get($rateLimitKey . ':expires', null);
    }

    public function getClientRateLimitInfo(string $clientId): array
    {
        $keys = [
            'api' => "api:{$clientId}",
            'webhook' => "webhook:{$clientId}",
            'export' => "export:{$clientId}",
        ];

        $info = [];
        foreach ($keys as $type => $key) {
            $attempts = Cache::get($key, 0);
            $expiresAt = Cache::get($key . ':expires', null);

            $info[$type] = [
                'attempts' => $attempts,
                'expires_at' => $expiresAt,
                'is_blocked' => $attempts > 0 && $expiresAt && now()->isBefore($expiresAt),
            ];
        }

        return $info;
    }

    public function clearClientRateLimits(string $clientId): void
    {
        $keys = [
            "api:{$clientId}",
            "webhook:{$clientId}",
            "export:{$clientId}",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
            Cache::forget($key . ':expires');
        }
    }

    public function getGlobalRateLimitInfo(): array
    {
        $keys = [
            'login' => 'login:*',
            'registration' => 'registration:*',
            'api' => 'api:*',
            'widget' => 'widget:*',
        ];

        $info = [];
        foreach ($keys as $type => $pattern) {
            // This would typically query a Redis or database to get actual counts
            // For now, return placeholder data
            $info[$type] = [
                'total_attempts' => 0,
                'active_blocks' => 0,
            ];
        }

        return $info;
    }

    public function getRateLimitConfig(): array
    {
        return [
            'api' => [
                'max_attempts' => config('sitepulse.rate_limit_per_minute', 60),
                'decay_minutes' => 1,
            ],
            'widget' => [
                'max_attempts' => 100,
                'decay_minutes' => 1,
            ],
            'login' => [
                'max_attempts' => 5,
                'decay_minutes' => 1,
            ],
            'registration' => [
                'max_attempts' => 3,
                'decay_minutes' => 60,
            ],
            'webhook' => [
                'max_attempts' => 100,
                'decay_minutes' => 1,
            ],
            'export' => [
                'max_attempts' => 10,
                'decay_minutes' => 60,
            ],
        ];
    }

    private function getRateLimitKey(Request $request, string $key): string
    {
        // Replace wildcards with actual values
        $key = str_replace('*', $request->ip(), $key);
        
        // Add timestamp for minute-based rate limiting
        $minute = now()->format('Y-m-d-H-i');
        
        return "rate_limit:{$key}:{$minute}";
    }
}
