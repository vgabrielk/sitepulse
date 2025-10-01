<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class SecurityService
{
    public function generateSecureApiKey(): string
    {
        do {
            $apiKey = 'sp_' . bin2hex(random_bytes(32));
        } while (Client::where('api_key', $apiKey)->exists());

        return $apiKey;
    }

    public function generateWebhookSecret(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function validateApiKey(string $apiKey): bool
    {
        return Client::where('api_key', $apiKey)
            ->where('is_active', true)
            ->exists();
    }

    public function validateWebhookSignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    public function hashSensitiveData(string $data): string
    {
        return hash('sha256', $data . config('app.key'));
    }

    public function encryptSensitiveData(string $data): string
    {
        return encrypt($data);
    }

    public function decryptSensitiveData(string $encryptedData): string
    {
        return decrypt($encryptedData);
    }

    public function sanitizeInput(string $input): string
    {
        // Remove potentially dangerous characters
        $input = strip_tags($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Trim whitespace
        $input = trim($input);
        
        return $input;
    }

    public function validateDomain(string $domain): bool
    {
        // Check if domain is valid
        if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return false;
        }

        // Check for suspicious patterns
        $suspiciousPatterns = [
            '/localhost/i',
            '/127\.0\.0\.1/i',
            '/0\.0\.0\.0/i',
            '/\.\./i', // Directory traversal
            '/<script/i', // XSS attempts
            '/javascript:/i', // JavaScript protocol
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $domain)) {
                return false;
            }
        }

        return true;
    }

    public function validateUrl(string $url): bool
    {
        // Check if URL is valid
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Check for suspicious patterns
        $suspiciousPatterns = [
            '/javascript:/i',
            '/data:/i',
            '/vbscript:/i',
            '/<script/i',
            '/\.\./i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return false;
            }
        }

        return true;
    }

    public function detectSuspiciousActivity(Request $request): bool
    {
        $suspiciousPatterns = [
            // SQL injection attempts
            '/union\s+select/i',
            '/drop\s+table/i',
            '/delete\s+from/i',
            '/insert\s+into/i',
            '/update\s+set/i',
            
            // XSS attempts
            '/<script/i',
            '/javascript:/i',
            '/onload=/i',
            '/onerror=/i',
            
            // Directory traversal
            '/\.\.\//i',
            '/\.\.\\\\/i',
            
            // Command injection
            '/;\s*rm\s/i',
            '/;\s*cat\s/i',
            '/;\s*ls\s/i',
            '/\|\s*cat/i',
            '/\|\s*rm/i',
        ];

        $input = $request->all();
        $inputString = json_encode($input);

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $inputString)) {
                Log::warning('Suspicious activity detected', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'pattern' => $pattern,
                    'input' => $inputString,
                ]);
                
                return true;
            }
        }

        return false;
    }

    public function blockSuspiciousIp(string $ip, int $durationMinutes = 60): void
    {
        $key = "blocked_ip:{$ip}";
        Cache::put($key, true, now()->addMinutes($durationMinutes));
        
        Log::warning('IP address blocked', [
            'ip' => $ip,
            'duration_minutes' => $durationMinutes,
        ]);
    }

    public function isIpBlocked(string $ip): bool
    {
        $key = "blocked_ip:{$ip}";
        return Cache::has($key);
    }

    public function unblockIp(string $ip): void
    {
        $key = "blocked_ip:{$ip}";
        Cache::forget($key);
        
        Log::info('IP address unblocked', ['ip' => $ip]);
    }

    public function getBlockedIps(): array
    {
        // This would typically query a database or Redis
        // For now, return empty array
        return [];
    }

    public function logSecurityEvent(string $event, array $data = []): void
    {
        Log::warning('Security event', [
            'event' => $event,
            'data' => $data,
            'timestamp' => now()->toISOString(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function generateCsrfToken(): string
    {
        return csrf_token();
    }

    public function validateCsrfToken(string $token): bool
    {
        return hash_equals(csrf_token(), $token);
    }

    public function generateNonce(): string
    {
        return base64_encode(random_bytes(16));
    }

    public function validateNonce(string $nonce): bool
    {
        // In a real implementation, you would check against a nonces table
        return true;
    }

    public function getSecurityHeaders(): array
    {
        return [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'",
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
        ];
    }

    public function auditLog(string $action, array $data = []): void
    {
        Log::info('Audit log', [
            'action' => $action,
            'data' => $data,
            'timestamp' => now()->toISOString(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id(),
        ]);
    }

    public function checkPasswordStrength(string $password): array
    {
        $score = 0;
        $feedback = [];

        // Length check
        if (strlen($password) >= 8) {
            $score += 1;
        } else {
            $feedback[] = 'Password should be at least 8 characters long';
        }

        // Uppercase check
        if (preg_match('/[A-Z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Password should contain at least one uppercase letter';
        }

        // Lowercase check
        if (preg_match('/[a-z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Password should contain at least one lowercase letter';
        }

        // Number check
        if (preg_match('/[0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Password should contain at least one number';
        }

        // Special character check
        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Password should contain at least one special character';
        }

        $strength = match (true) {
            $score >= 4 => 'strong',
            $score >= 3 => 'medium',
            default => 'weak',
        };

        return [
            'score' => $score,
            'strength' => $strength,
            'feedback' => $feedback,
        ];
    }

    public function generateSecurePassword(int $length = 16): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }
}
