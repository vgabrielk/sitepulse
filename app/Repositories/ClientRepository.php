<?php

namespace App\Repositories;

use App\Models\Client;
use App\DTOs\ClientDTO;
use Illuminate\Database\Eloquent\Collection;

class ClientRepository
{
    public function findById(int $id): ?Client
    {
        return Client::find($id);
    }

    public function findByApiKey(string $apiKey): ?Client
    {
        return Client::where('api_key', $apiKey)->first();
    }

    public function findByEmail(string $email): ?Client
    {
        return Client::where('email', $email)->first();
    }

    public function create(array $data): Client
    {
        return Client::create($data);
    }

    public function update(Client $client, array $data): bool
    {
        return $client->update($data);
    }

    public function delete(Client $client): bool
    {
        return $client->delete();
    }

    public function getAll(int $limit = 50, int $offset = 0): Collection
    {
        return Client::limit($limit)->offset($offset)->get();
    }

    public function getActiveClients(): Collection
    {
        return Client::where('is_active', true)->get();
    }

    public function getClientsByPlan(string $plan): Collection
    {
        return Client::where('plan', $plan)->get();
    }

    public function getTrialClients(): Collection
    {
        return Client::whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->get();
    }

    public function getExpiredTrialClients(): Collection
    {
        return Client::whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', now())
            ->get();
    }

    public function getClientsWithExpiringTrials(int $days = 7): Collection
    {
        return Client::whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->where('trial_ends_at', '<=', now()->addDays($days))
            ->get();
    }

    public function updatePlanLimits(Client $client, array $limits): bool
    {
        return $client->update(['plan_limits' => $limits]);
    }

    public function updateSettings(Client $client, array $settings): bool
    {
        return $client->update(['settings' => $settings]);
    }

    public function activate(Client $client): bool
    {
        return $client->update(['is_active' => true]);
    }

    public function deactivate(Client $client): bool
    {
        return $client->update(['is_active' => false]);
    }

    public function generateApiKey(): string
    {
        do {
            $apiKey = 'sp_' . bin2hex(random_bytes(32));
        } while ($this->findByApiKey($apiKey));

        return $apiKey;
    }

    public function getClientStats(Client $client): array
    {
        $sites = $client->sites()->count();
        $totalSessions = $client->sites()->withCount('sessions')->get()->sum('sessions_count');
        $totalVisits = $client->sites()->withCount('visits')->get()->sum('visits_count');
        $totalReviews = $client->sites()->withCount('reviews')->get()->sum('reviews_count');

        return [
            'sites_count' => $sites,
            'total_sessions' => $totalSessions,
            'total_visits' => $totalVisits,
            'total_reviews' => $totalReviews,
        ];
    }
}
