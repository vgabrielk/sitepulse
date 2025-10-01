<?php

namespace App\DTOs;

class ReviewDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $siteId,
        public readonly ?int $sessionId,
        public readonly ?string $visitorName,
        public readonly ?string $visitorEmail,
        public readonly int $rating,
        public readonly ?string $comment,
        public readonly string $status,
        public readonly ?string $ipAddress,
        public readonly ?array $metadata,
        public readonly string $submittedAt,
        public readonly ?string $approvedAt,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public static function fromModel(\App\Models\Review $review): self
    {
        return new self(
            id: $review->id,
            siteId: $review->site_id,
            sessionId: $review->session_id,
            visitorName: $review->visitor_name,
            visitorEmail: $review->visitor_email,
            rating: $review->rating,
            comment: $review->comment,
            status: $review->status,
            ipAddress: $review->ip_address,
            metadata: $review->metadata,
            submittedAt: $review->submitted_at->toISOString(),
            approvedAt: $review->approved_at?->toISOString(),
            createdAt: $review->created_at->toISOString(),
            updatedAt: $review->updated_at->toISOString(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'site_id' => $this->siteId,
            'session_id' => $this->sessionId,
            'visitor_name' => $this->visitorName,
            'visitor_email' => $this->visitorEmail,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'status' => $this->status,
            'ip_address' => $this->ipAddress,
            'metadata' => $this->metadata,
            'submitted_at' => $this->submittedAt,
            'approved_at' => $this->approvedAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
