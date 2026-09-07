<?php

declare(strict_types=1);

namespace Liberu\Accounting\BusinessInsightsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class InsightResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-insights', 'attributes' => ['metric' => $this->metric, 'period_start' => $this->period_start?->toDateString(), 'period_end' => $this->period_end?->toDateString(), 'value' => $this->value, 'comparison_value' => $this->comparison_value, 'unit' => $this->unit, 'explanation' => $this->explanation, 'metadata' => $this->metadata, 'refreshed_at' => $this->refreshed_at?->toIso8601String()]];
    }
}
