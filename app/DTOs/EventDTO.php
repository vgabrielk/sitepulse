<?php

namespace App\DTOs;

class EventDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $visitId,
        public readonly string $eventType,
        public readonly ?string $elementSelector,
        public readonly ?string $elementText,
        public readonly ?string $elementTag,
        public readonly ?array $coordinates,
        public readonly ?array $eventData,
        public readonly string $occurredAt,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public static function fromModel(\App\Models\Event $event): self
    {
        return new self(
            id: $event->id,
            visitId: $event->visit_id,
            eventType: $event->event_type,
            elementSelector: $event->element_selector,
            elementText: $event->element_text,
            elementTag: $event->element_tag,
            coordinates: $event->coordinates,
            eventData: $event->event_data,
            occurredAt: $event->occurred_at->toISOString(),
            createdAt: $event->created_at->toISOString(),
            updatedAt: $event->updated_at->toISOString(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'visit_id' => $this->visitId,
            'event_type' => $this->eventType,
            'element_selector' => $this->elementSelector,
            'element_text' => $this->elementText,
            'element_tag' => $this->elementTag,
            'coordinates' => $this->coordinates,
            'event_data' => $this->eventData,
            'occurred_at' => $this->occurredAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
