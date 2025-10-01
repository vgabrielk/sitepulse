<?php

namespace App\DTOs;

class SiteDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $clientId,
        public readonly string $name,
        public readonly string $domain,
        public readonly string $widgetId,
        public readonly array $widgetConfig,
        public readonly array $trackingConfig,
        public readonly bool $isActive,
        public readonly bool $anonymizeIps,
        public readonly bool $trackEvents,
        public readonly bool $collectFeedback,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public static function fromModel(\App\Models\Site $site): self
    {
        return new self(
            id: $site->id,
            clientId: $site->client_id,
            name: $site->name,
            domain: $site->domain,
            widgetId: $site->widget_id,
            widgetConfig: $site->widget_config ?? [],
            trackingConfig: $site->tracking_config ?? [],
            isActive: $site->is_active,
            anonymizeIps: $site->anonymize_ips,
            trackEvents: $site->track_events,
            collectFeedback: $site->collect_feedback,
            createdAt: $site->created_at->toISOString(),
            updatedAt: $site->updated_at->toISOString(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->clientId,
            'name' => $this->name,
            'domain' => $this->domain,
            'widget_id' => $this->widgetId,
            'widget_config' => $this->widgetConfig,
            'tracking_config' => $this->trackingConfig,
            'is_active' => $this->isActive,
            'anonymize_ips' => $this->anonymizeIps,
            'track_events' => $this->trackEvents,
            'collect_feedback' => $this->collectFeedback,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
