<?php

namespace App\Services;

use App\Models\Client;
use App\Repositories\ClientRepository;
use Illuminate\Support\Facades\Log;

class PlanService
{
    public function __construct(
        private ClientRepository $clientRepository
    ) {}

    public function getAvailablePlans(): array
    {
        return [
            'free' => [
                'name' => 'Free',
                'description' => 'Perfect for getting started',
                'price' => 0,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'limits' => [
                    'monthly_visits' => 1000,
                    'monthly_events' => 5000,
                    'sites' => 1,
                    'reviews' => 50,
                    'exports' => 0,
                    'api_calls' => 1000,
                    'retention_days' => 30,
                ],
                'features' => [
                    'Basic analytics',
                    'Widget embed',
                    'Email support',
                    '30-day data retention',
                ],
                'popular' => false,
            ],
            'basic' => [
                'name' => 'Basic',
                'description' => 'Great for small websites',
                'price' => 9.99,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'limits' => [
                    'monthly_visits' => 10000,
                    'monthly_events' => 50000,
                    'sites' => 3,
                    'reviews' => 500,
                    'exports' => 10,
                    'api_calls' => 10000,
                    'retention_days' => 90,
                ],
                'features' => [
                    'Advanced analytics',
                    'Custom widgets',
                    'Priority support',
                    '90-day data retention',
                    'Basic exports',
                    'Webhook integrations',
                ],
                'popular' => true,
            ],
            'premium' => [
                'name' => 'Premium',
                'description' => 'Perfect for growing businesses',
                'price' => 29.99,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'limits' => [
                    'monthly_visits' => 100000,
                    'monthly_events' => 500000,
                    'sites' => 10,
                    'reviews' => 5000,
                    'exports' => 100,
                    'api_calls' => 100000,
                    'retention_days' => 365,
                ],
                'features' => [
                    'Premium analytics',
                    'Custom branding',
                    'Priority support',
                    '1-year data retention',
                    'Advanced exports',
                    'API access',
                    'Webhook integrations',
                    'Heatmaps',
                    'A/B testing',
                ],
                'popular' => false,
            ],
            'enterprise' => [
                'name' => 'Enterprise',
                'description' => 'For large organizations',
                'price' => 99.99,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'limits' => [
                    'monthly_visits' => -1, // unlimited
                    'monthly_events' => -1,
                    'sites' => -1,
                    'reviews' => -1,
                    'exports' => -1,
                    'api_calls' => -1,
                    'retention_days' => -1, // unlimited
                ],
                'features' => [
                    'Unlimited everything',
                    'White-label solution',
                    'Dedicated support',
                    'Unlimited data retention',
                    'Custom integrations',
                    'SLA guarantee',
                    'On-premise option',
                    'Custom reporting',
                ],
                'popular' => false,
            ],
        ];
    }

    public function getPlanDetails(string $planName): ?array
    {
        $plans = $this->getAvailablePlans();
        return $plans[$planName] ?? null;
    }

    public function upgradeClient(Client $client, string $newPlan): bool
    {
        $planDetails = $this->getPlanDetails($newPlan);
        
        if (!$planDetails) {
            return false;
        }

        try {
            $this->clientRepository->update($client, [
                'plan' => $newPlan,
                'plan_limits' => $planDetails['limits'],
                'subscription_ends_at' => now()->addMonth(),
            ]);

            // Log the upgrade
            Log::info('Client upgraded plan', [
                'client_id' => $client->id,
                'old_plan' => $client->plan,
                'new_plan' => $newPlan,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to upgrade client plan', [
                'client_id' => $client->id,
                'new_plan' => $newPlan,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    public function downgradeClient(Client $client, string $newPlan): bool
    {
        $planDetails = $this->getPlanDetails($newPlan);
        
        if (!$planDetails) {
            return false;
        }

        // Check if client exceeds new plan limits
        if (!$this->canDowngradeToPlan($client, $newPlan)) {
            return false;
        }

        try {
            $this->clientRepository->update($client, [
                'plan' => $newPlan,
                'plan_limits' => $planDetails['limits'],
                'subscription_ends_at' => now()->addMonth(),
            ]);

            // Log the downgrade
            Log::info('Client downgraded plan', [
                'client_id' => $client->id,
                'old_plan' => $client->plan,
                'new_plan' => $newPlan,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to downgrade client plan', [
                'client_id' => $client->id,
                'new_plan' => $newPlan,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    public function canDowngradeToPlan(Client $client, string $newPlan): bool
    {
        $newPlanDetails = $this->getPlanDetails($newPlan);
        
        if (!$newPlanDetails) {
            return false;
        }

        $currentUsage = $this->getCurrentUsage($client);
        $newLimits = $newPlanDetails['limits'];

        // Check each limit
        foreach ($newLimits as $limitKey => $limitValue) {
            if ($limitValue === -1) {
                continue; // Unlimited
            }

            $currentValue = $currentUsage[$limitKey] ?? 0;
            if ($currentValue > $limitValue) {
                return false;
            }
        }

        return true;
    }

    public function getCurrentUsage(Client $client): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return [
            'monthly_visits' => $client->sites()
                ->withCount(['visits' => function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('visited_at', [$startOfMonth, $endOfMonth]);
                }])
                ->get()
                ->sum('visits_count'),
            
            'monthly_events' => $client->sites()
                ->withCount(['events' => function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('occurred_at', [$startOfMonth, $endOfMonth]);
                }])
                ->get()
                ->sum('events_count'),
            
            'sites' => $client->sites()->count(),
            
            'reviews' => $client->sites()
                ->withCount(['reviews' => function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('submitted_at', [$startOfMonth, $endOfMonth]);
                }])
                ->get()
                ->sum('reviews_count'),
            
            'exports' => $client->exports_used ?? 0,
            
            'api_calls' => $client->api_calls_used ?? 0,
        ];
    }

    public function checkPlanLimits(Client $client, string $action, int $value = 1): bool
    {
        $planLimits = $client->getPlanLimits();
        $currentUsage = $this->getCurrentUsage($client);
        
        $limitKey = $this->getLimitKeyForAction($action);
        
        if (!isset($planLimits[$limitKey])) {
            return true; // No limit defined
        }
        
        $limit = $planLimits[$limitKey];
        
        if ($limit === -1) {
            return true; // Unlimited
        }
        
        $currentValue = $currentUsage[$limitKey] ?? 0;
        
        return ($currentValue + $value) <= $limit;
    }

    public function incrementUsage(Client $client, string $action, int $value = 1): void
    {
        $limitKey = $this->getLimitKeyForAction($action);
        
        // Update usage counters (you might want to store these in a separate table)
        switch ($limitKey) {
            case 'exports':
                $client->increment('exports_used', $value);
                break;
            case 'api_calls':
                $client->increment('api_calls_used', $value);
                break;
        }
    }

    public function getUsagePercentage(Client $client, string $action): float
    {
        $planLimits = $client->getPlanLimits();
        $currentUsage = $this->getCurrentUsage($client);
        
        $limitKey = $this->getLimitKeyForAction($action);
        
        if (!isset($planLimits[$limitKey]) || $planLimits[$limitKey] === -1) {
            return 0; // No limit or unlimited
        }
        
        $currentValue = $currentUsage[$limitKey] ?? 0;
        $limit = $planLimits[$limitKey];
        
        return min(($currentValue / $limit) * 100, 100);
    }

    public function getBillingInfo(Client $client): array
    {
        $planDetails = $this->getPlanDetails($client->plan);
        
        return [
            'current_plan' => $client->plan,
            'plan_details' => $planDetails,
            'subscription_ends_at' => $client->subscription_ends_at,
            'trial_ends_at' => $client->trial_ends_at,
            'is_on_trial' => $client->isOnTrial(),
            'has_active_subscription' => $client->hasActiveSubscription(),
            'usage' => $this->getCurrentUsage($client),
            'usage_percentages' => [
                'monthly_visits' => $this->getUsagePercentage($client, 'visits'),
                'monthly_events' => $this->getUsagePercentage($client, 'events'),
                'sites' => $this->getUsagePercentage($client, 'sites'),
                'reviews' => $this->getUsagePercentage($client, 'reviews'),
                'exports' => $this->getUsagePercentage($client, 'exports'),
                'api_calls' => $this->getUsagePercentage($client, 'api_calls'),
            ],
        ];
    }

    private function getLimitKeyForAction(string $action): string
    {
        return match ($action) {
            'visits' => 'monthly_visits',
            'events' => 'monthly_events',
            'sites' => 'sites',
            'reviews' => 'reviews',
            'exports' => 'exports',
            'api_calls' => 'api_calls',
            default => 'monthly_visits',
        };
    }
}
