<?php

namespace App\DTOs;

class SessionDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $siteId,
        public readonly string $sessionToken,
        public readonly ?string $visitorId,
        public readonly ?string $ipAddress,
        public readonly ?string $userAgent,
        public readonly ?string $country,
        public readonly ?string $city,
        public readonly ?string $referrer,
        public readonly ?string $utmSource,
        public readonly ?string $utmMedium,
        public readonly ?string $utmCampaign,
        public readonly array $deviceInfo,
        public readonly string $startedAt,
        public readonly string $lastActivityAt,
        public readonly ?string $endedAt,
        public readonly ?int $durationSeconds,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public static function fromModel(\App\Models\Session $session): self
    {
        return new self(
            id: $session->id,
            siteId: $session->site_id,
            sessionToken: $session->session_token,
            visitorId: $session->visitor_id,
            ipAddress: $session->ip_address,
            userAgent: $session->user_agent,
            country: $session->country,
            city: $session->city,
            referrer: $session->referrer,
            utmSource: $session->utm_source,
            utmMedium: $session->utm_medium,
            utmCampaign: $session->utm_campaign,
            deviceInfo: $session->device_info ?? [],
            startedAt: $session->started_at->toISOString(),
            lastActivityAt: $session->last_activity_at->toISOString(),
            endedAt: $session->ended_at?->toISOString(),
            durationSeconds: $session->duration_seconds,
            createdAt: $session->created_at->toISOString(),
            updatedAt: $session->updated_at->toISOString(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'site_id' => $this->siteId,
            'session_token' => $this->sessionToken,
            'visitor_id' => $this->visitorId,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'country' => $this->country,
            'city' => $this->city,
            'referrer' => $this->referrer,
            'utm_source' => $this->utmSource,
            'utm_medium' => $this->utmMedium,
            'utm_campaign' => $this->utmCampaign,
            'device_info' => $this->deviceInfo,
            'started_at' => $this->startedAt,
            'last_activity_at' => $this->lastActivityAt,
            'ended_at' => $this->endedAt,
            'duration_seconds' => $this->durationSeconds,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
