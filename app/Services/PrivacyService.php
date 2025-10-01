<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Session;
use App\Models\Visit;
use App\Models\Event;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class PrivacyService
{
    public function anonymizeIpAddress(string $ipAddress): string
    {
        // IPv4: Remove last octet
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ipAddress);
            $parts[3] = '0';
            return implode('.', $parts);
        }
        
        // IPv6: Remove last 4 groups
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ipAddress);
            $parts = array_slice($parts, 0, -4);
            $parts[] = '0:0:0:0';
            return implode(':', $parts);
        }
        
        return $ipAddress;
    }

    public function hashIpAddress(string $ipAddress): string
    {
        return hash('sha256', $ipAddress . config('app.key'));
    }

    public function anonymizeUserAgent(string $userAgent): string
    {
        // Remove version numbers and specific details
        $patterns = [
            '/\d+\.\d+\.\d+/',  // Version numbers
            '/\([^)]*\)/',       // Parentheses content
            '/\b\d{4}\b/',       // Years
        ];
        
        $anonymized = $userAgent;
        foreach ($patterns as $pattern) {
            $anonymized = preg_replace($pattern, 'X', $anonymized);
        }
        
        return $anonymized;
    }

    public function anonymizeEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) === 2) {
            $local = $parts[0];
            $domain = $parts[1];
            
            // Keep first and last character of local part
            if (strlen($local) > 2) {
                $local = $local[0] . str_repeat('*', strlen($local) - 2) . $local[-1];
            } else {
                $local = str_repeat('*', strlen($local));
            }
            
            return $local . '@' . $domain;
        }
        
        return $email;
    }

    public function anonymizeName(string $name): string
    {
        $words = explode(' ', trim($name));
        $anonymized = [];
        
        foreach ($words as $word) {
            if (strlen($word) > 2) {
                $anonymized[] = $word[0] . str_repeat('*', strlen($word) - 2) . $word[-1];
            } else {
                $anonymized[] = str_repeat('*', strlen($word));
            }
        }
        
        return implode(' ', $anonymized);
    }

    public function anonymizeClientData(Client $client): void
    {
        try {
            DB::transaction(function () use ($client) {
                // Anonymize sessions
                Session::where('site_id', function ($query) use ($client) {
                    $query->select('id')->from('sites')->where('client_id', $client->id);
                })->update([
                    'ip_address' => null,
                    'user_agent' => 'Anonymized',
                    'country' => null,
                    'city' => null,
                ]);

                // Anonymize visits
                Visit::whereHas('session.site', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })->update([
                    'title' => 'Anonymized',
                ]);

                // Anonymize events
                Event::whereHas('visit.session.site', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })->update([
                    'element_text' => 'Anonymized',
                    'event_data' => null,
                ]);

                // Anonymize reviews
                Review::whereHas('site', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })->update([
                    'visitor_name' => 'Anonymous',
                    'visitor_email' => null,
                    'comment' => 'Anonymized',
                    'ip_address' => null,
                ]);

                Log::info('Client data anonymized', ['client_id' => $client->id]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to anonymize client data', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function deleteClientData(Client $client): void
    {
        try {
            DB::transaction(function () use ($client) {
                // Delete all related data
                $client->sites()->each(function ($site) {
                    $site->sessions()->delete();
                    $site->pages()->delete();
                    $site->reviews()->delete();
                    $site->surveys()->delete();
                    $site->metrics()->delete();
                });
                
                $client->sites()->delete();
                $client->delete();

                Log::info('Client data deleted', ['client_id' => $client->id]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to delete client data', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function exportClientData(Client $client): array
    {
        $data = [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'plan' => $client->plan,
                'created_at' => $client->created_at,
                'updated_at' => $client->updated_at,
            ],
            'sites' => $client->sites()->get()->map(function ($site) {
                return [
                    'id' => $site->id,
                    'name' => $site->name,
                    'domain' => $site->domain,
                    'widget_id' => $site->widget_id,
                    'is_active' => $site->is_active,
                    'created_at' => $site->created_at,
                    'updated_at' => $site->updated_at,
                ];
            }),
            'sessions' => $client->sites()->with('sessions')->get()
                ->pluck('sessions')
                ->flatten()
                ->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'session_token' => $session->session_token,
                        'started_at' => $session->started_at,
                        'ended_at' => $session->ended_at,
                        'duration_seconds' => $session->duration_seconds,
                    ];
                }),
            'reviews' => $client->sites()->with('reviews')->get()
                ->pluck('reviews')
                ->flatten()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'status' => $review->status,
                        'submitted_at' => $review->submitted_at,
                    ];
                }),
        ];

        return $data;
    }

    public function checkDataRetention(Client $client): void
    {
        $planLimits = $client->getPlanLimits();
        $retentionDays = $planLimits['retention_days'] ?? 365;
        
        if ($retentionDays === -1) {
            return; // Unlimited retention
        }

        $cutoffDate = now()->subDays($retentionDays);

        try {
            DB::transaction(function () use ($client, $cutoffDate) {
                // Delete old sessions
                Session::whereHas('site', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })->where('started_at', '<', $cutoffDate)->delete();

                // Delete old visits
                Visit::whereHas('session.site', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })->where('visited_at', '<', $cutoffDate)->delete();

                // Delete old events
                Event::whereHas('visit.session.site', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })->where('occurred_at', '<', $cutoffDate)->delete();

                // Delete old reviews
                Review::whereHas('site', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })->where('submitted_at', '<', $cutoffDate)->delete();

                // Delete old metrics
                DB::table('metrics')
                    ->where('site_id', function ($query) use ($client) {
                        $query->select('id')->from('sites')->where('client_id', $client->id);
                    })
                    ->where('date', '<', $cutoffDate->format('Y-m-d'))
                    ->delete();

                Log::info('Data retention cleanup completed', [
                    'client_id' => $client->id,
                    'cutoff_date' => $cutoffDate,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to cleanup old data', [
                'client_id' => $client->id,
                'cutoff_date' => $cutoffDate,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function isDataProcessingConsentRequired(string $country): bool
    {
        $euCountries = [
            'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR',
            'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL',
            'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB'
        ];

        return in_array(strtoupper($country), $euCountries);
    }

    public function getPrivacyPolicyUrl(): string
    {
        return config('app.url') . '/privacy-policy';
    }

    public function getTermsOfServiceUrl(): string
    {
        return config('app.url') . '/terms-of-service';
    }

    public function getCookiePolicyUrl(): string
    {
        return config('app.url') . '/cookie-policy';
    }

    public function generateConsentToken(): string
    {
        return hash('sha256', uniqid() . config('app.key'));
    }

    public function validateConsentToken(string $token): bool
    {
        // In a real implementation, you would check against a consent_tokens table
        return true;
    }

    public function logDataProcessingActivity(string $activity, array $data = []): void
    {
        Log::info('Data processing activity', [
            'activity' => $activity,
            'data' => $data,
            'timestamp' => now()->toISOString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
