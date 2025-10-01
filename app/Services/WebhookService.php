<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WebhookService
{
    public function sendWebhook(Client $client, string $event, array $data): bool
    {
        if (!$client->webhook_url) {
            return false;
        }

        try {
            $payload = [
                'event' => $event,
                'timestamp' => now()->toISOString(),
                'data' => $data,
                'client_id' => $client->id,
            ];

            $signature = $this->generateSignature($payload, $client->webhook_secret);

            $response = Http::timeout(10)
                ->withHeaders([
                    'X-SitePulse-Signature' => $signature,
                    'X-SitePulse-Event' => $event,
                    'Content-Type' => 'application/json',
                ])
                ->post($client->webhook_url, $payload);

            if ($response->successful()) {
                Log::info('Webhook sent successfully', [
                    'client_id' => $client->id,
                    'event' => $event,
                    'url' => $client->webhook_url,
                ]);
                return true;
            } else {
                Log::warning('Webhook failed', [
                    'client_id' => $client->id,
                    'event' => $event,
                    'url' => $client->webhook_url,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Webhook error', [
                'client_id' => $client->id,
                'event' => $event,
                'url' => $client->webhook_url,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function sendEventWebhook(Client $client, array $eventData): bool
    {
        return $this->sendWebhook($client, 'event.created', $eventData);
    }

    public function sendSessionWebhook(Client $client, array $sessionData): bool
    {
        return $this->sendWebhook($client, 'session.created', $sessionData);
    }

    public function sendReviewWebhook(Client $client, array $reviewData): bool
    {
        return $this->sendWebhook($client, 'review.created', $reviewData);
    }

    public function sendSiteWebhook(Client $client, array $siteData): bool
    {
        return $this->sendWebhook($client, 'site.created', $siteData);
    }

    public function sendLimitExceededWebhook(Client $client, string $limitType, int $currentUsage, int $limit): bool
    {
        return $this->sendWebhook($client, 'limit.exceeded', [
            'limit_type' => $limitType,
            'current_usage' => $currentUsage,
            'limit' => $limit,
            'percentage' => ($currentUsage / $limit) * 100,
        ]);
    }

    public function sendPlanUpgradeWebhook(Client $client, string $oldPlan, string $newPlan): bool
    {
        return $this->sendWebhook($client, 'plan.upgraded', [
            'old_plan' => $oldPlan,
            'new_plan' => $newPlan,
            'upgraded_at' => now()->toISOString(),
        ]);
    }

    public function sendPlanDowngradeWebhook(Client $client, string $oldPlan, string $newPlan): bool
    {
        return $this->sendWebhook($client, 'plan.downgraded', [
            'old_plan' => $oldPlan,
            'new_plan' => $newPlan,
            'downgraded_at' => now()->toISOString(),
        ]);
    }

    public function sendSubscriptionCancelledWebhook(Client $client, string $reason = null): bool
    {
        return $this->sendWebhook($client, 'subscription.cancelled', [
            'cancelled_at' => now()->toISOString(),
            'reason' => $reason,
        ]);
    }

    public function sendSubscriptionReactivatedWebhook(Client $client): bool
    {
        return $this->sendWebhook($client, 'subscription.reactivated', [
            'reactivated_at' => now()->toISOString(),
        ]);
    }

    public function sendDailyReportWebhook(Client $client, array $reportData): bool
    {
        return $this->sendWebhook($client, 'report.daily', $reportData);
    }

    public function sendWeeklyReportWebhook(Client $client, array $reportData): bool
    {
        return $this->sendWebhook($client, 'report.weekly', $reportData);
    }

    public function sendMonthlyReportWebhook(Client $client, array $reportData): bool
    {
        return $this->sendWebhook($client, 'report.monthly', $reportData);
    }

    public function validateWebhookSignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = $this->generateSignature(json_decode($payload, true), $secret);
        return hash_equals($expectedSignature, $signature);
    }

    public function testWebhook(Client $client): bool
    {
        $testData = [
            'test' => true,
            'message' => 'This is a test webhook from SitePulse',
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendWebhook($client, 'test', $testData);
    }

    public function getWebhookEvents(): array
    {
        return [
            'event.created' => 'New event tracked',
            'session.created' => 'New session started',
            'review.created' => 'New review submitted',
            'site.created' => 'New site added',
            'limit.exceeded' => 'Plan limit exceeded',
            'plan.upgraded' => 'Plan upgraded',
            'plan.downgraded' => 'Plan downgraded',
            'subscription.cancelled' => 'Subscription cancelled',
            'subscription.reactivated' => 'Subscription reactivated',
            'report.daily' => 'Daily report',
            'report.weekly' => 'Weekly report',
            'report.monthly' => 'Monthly report',
            'test' => 'Test webhook',
        ];
    }

    public function getWebhookStats(Client $client): array
    {
        $cacheKey = "webhook_stats_{$client->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($client) {
            return [
                'total_sent' => $this->getWebhookCount($client, 'sent'),
                'total_failed' => $this->getWebhookCount($client, 'failed'),
                'last_sent' => $this->getLastWebhookTime($client),
                'success_rate' => $this->getWebhookSuccessRate($client),
            ];
        });
    }

    private function generateSignature(array $payload, string $secret): string
    {
        $payloadString = json_encode($payload);
        return 'sha256=' . hash_hmac('sha256', $payloadString, $secret);
    }

    private function getWebhookCount(Client $client, string $status): int
    {
        // This would typically query a webhook_logs table
        // For now, return a placeholder
        return 0;
    }

    private function getLastWebhookTime(Client $client): ?string
    {
        // This would typically query a webhook_logs table
        // For now, return a placeholder
        return null;
    }

    private function getWebhookSuccessRate(Client $client): float
    {
        $totalSent = $this->getWebhookCount($client, 'sent');
        $totalFailed = $this->getWebhookCount($client, 'failed');
        
        if ($totalSent + $totalFailed === 0) {
            return 0;
        }
        
        return ($totalSent / ($totalSent + $totalFailed)) * 100;
    }
}
