<?php

namespace App\DTOs;

class MetricsDTO
{
    public function __construct(
        public readonly int $siteId,
        public readonly string $period,
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly array $metrics,
        public readonly array $summary,
    ) {}

    public function toArray(): array
    {
        return [
            'site_id' => $this->siteId,
            'period' => $this->period,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'metrics' => $this->metrics,
            'summary' => $this->summary,
        ];
    }

    public static function create(
        int $siteId,
        string $period,
        string $startDate,
        string $endDate,
        array $metrics,
        array $summary
    ): self {
        return new self(
            siteId: $siteId,
            period: $period,
            startDate: $startDate,
            endDate: $endDate,
            metrics: $metrics,
            summary: $summary,
        );
    }
}
