<?php

namespace App\Services;

use App\Models\Client;
use App\Repositories\ClientRepository;
use App\DTOs\ClientDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class ClientService
{
    public function __construct(
        private ClientRepository $clientRepository
    ) {}

    public function createClient(array $data): ClientDTO
    {
        $data['api_key'] = $this->clientRepository->generateApiKey();
        $data['plan_limits'] = $this->getDefaultPlanLimits($data['plan'] ?? 'free');
        
        $client = $this->clientRepository->create($data);
        
        return ClientDTO::fromModel($client);
    }

    public function getClientById(int $id): ?ClientDTO
    {
        $client = $this->clientRepository->findById($id);
        
        return $client ? ClientDTO::fromModel($client) : null;
    }

    public function getClientByApiKey(string $apiKey): ?ClientDTO
    {
        $client = $this->clientRepository->findByApiKey($apiKey);
        
        return $client ? ClientDTO::fromModel($client) : null;
    }

    public function updateClient(Client $client, array $data): ClientDTO
    {
        $this->clientRepository->update($client, $data);
        
        return ClientDTO::fromModel($client->fresh());
    }

    public function deleteClient(Client $client): bool
    {
        return $this->clientRepository->delete($client);
    }

    public function getAllClients(int $limit = 50, int $offset = 0): array
    {
        $clients = $this->clientRepository->getAll($limit, $offset);
        
        return $clients->map(fn($client) => ClientDTO::fromModel($client))->toArray();
    }

    public function getActiveClients(): array
    {
        $clients = $this->clientRepository->getActiveClients();
        
        return $clients->map(fn($client) => ClientDTO::fromModel($client))->toArray();
    }

    public function getClientsByPlan(string $plan): array
    {
        $clients = $this->clientRepository->getClientsByPlan($plan);
        
        return $clients->map(fn($client) => ClientDTO::fromModel($client))->toArray();
    }

    public function updatePlan(Client $client, string $plan): ClientDTO
    {
        $planLimits = $this->getDefaultPlanLimits($plan);
        
        $this->clientRepository->update($client, [
            'plan' => $plan,
            'plan_limits' => $planLimits,
        ]);
        
        return ClientDTO::fromModel($client->fresh());
    }

    public function updateSettings(Client $client, array $settings): ClientDTO
    {
        $this->clientRepository->updateSettings($client, $settings);
        
        return ClientDTO::fromModel($client->fresh());
    }

    public function activateClient(Client $client): ClientDTO
    {
        $this->clientRepository->activate($client);
        
        return ClientDTO::fromModel($client->fresh());
    }

    public function deactivateClient(Client $client): ClientDTO
    {
        $this->clientRepository->deactivate($client);
        
        return ClientDTO::fromModel($client->fresh());
    }

    public function getClientStats(Client $client): array
    {
        return $this->clientRepository->getClientStats($client);
    }

    public function checkPlanLimits(Client $client, string $metricType, int $value = 1): bool
    {
        $limits = $client->getPlanLimits();
        $limitKey = $this->getLimitKey($metricType);
        
        if (!isset($limits[$limitKey])) {
            return true; // No limit defined
        }
        
        $limit = $limits[$limitKey];
        
        if ($limit === -1) {
            return true; // Unlimited
        }
        
        // Get current usage for the month
        $currentUsage = $this->getCurrentUsage($client, $metricType);
        
        return ($currentUsage + $value) <= $limit;
    }

    public function getCurrentUsage(Client $client, string $metricType): int
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        return match ($metricType) {
            'visits' => $client->sites()->withCount(['visits' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('visited_at', [$startOfMonth, $endOfMonth]);
            }])->get()->sum('visits_count'),
            
            'events' => $client->sites()->withCount(['events' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('occurred_at', [$startOfMonth, $endOfMonth]);
            }])->get()->sum('events_count'),
            
            'reviews' => $client->sites()->withCount(['reviews' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('submitted_at', [$startOfMonth, $endOfMonth]);
            }])->get()->sum('reviews_count'),
            
            'sites' => $client->sites()->count(),
            
            default => 0,
        };
    }

    private function getDefaultPlanLimits(string $plan): array
    {
        return match ($plan) {
            'free' => [
                'monthly_visits' => 1000,
                'monthly_events' => 5000,
                'sites' => 1,
                'reviews' => 50,
                'exports' => 0,
            ],
            'basic' => [
                'monthly_visits' => 10000,
                'monthly_events' => 50000,
                'sites' => 3,
                'reviews' => 500,
                'exports' => 10,
            ],
            'premium' => [
                'monthly_visits' => 100000,
                'monthly_events' => 500000,
                'sites' => 10,
                'reviews' => 5000,
                'exports' => 100,
            ],
            'enterprise' => [
                'monthly_visits' => -1,
                'monthly_events' => -1,
                'sites' => -1,
                'reviews' => -1,
                'exports' => -1,
            ],
            default => [
                'monthly_visits' => 1000,
                'monthly_events' => 5000,
                'sites' => 1,
                'reviews' => 50,
                'exports' => 0,
            ],
        };
    }

    private function getLimitKey(string $metricType): string
    {
        return match ($metricType) {
            'visits' => 'monthly_visits',
            'events' => 'monthly_events',
            'reviews' => 'reviews',
            'sites' => 'sites',
            default => 'monthly_visits',
        };
    }
}
