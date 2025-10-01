<?php

namespace App\DTOs;

class ClientDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $apiKey,
        public readonly ?string $webhookUrl,
        public readonly ?string $webhookSecret,
        public readonly string $plan,
        public readonly array $planLimits,
        public readonly array $settings,
        public readonly bool $isActive,
        public readonly ?string $trialEndsAt,
        public readonly ?string $subscriptionEndsAt,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public static function fromModel(\App\Models\Client $client): self
    {
        return new self(
            id: $client->id,
            name: $client->name,
            email: $client->email,
            apiKey: $client->api_key,
            webhookUrl: $client->webhook_url,
            webhookSecret: $client->webhook_secret,
            plan: $client->plan,
            planLimits: $client->getPlanLimits(),
            settings: $client->settings ?? [],
            isActive: (bool) $client->is_active,
            trialEndsAt: $client->trial_ends_at?->toISOString(),
            subscriptionEndsAt: $client->subscription_ends_at?->toISOString(),
            createdAt: $client->created_at->toISOString(),
            updatedAt: $client->updated_at->toISOString(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'api_key' => $this->apiKey,
            'webhook_url' => $this->webhookUrl,
            'webhook_secret' => $this->webhookSecret,
            'plan' => $this->plan,
            'plan_limits' => $this->planLimits,
            'settings' => $this->settings,
            'is_active' => $this->isActive,
            'trial_ends_at' => $this->trialEndsAt,
            'subscription_ends_at' => $this->subscriptionEndsAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
