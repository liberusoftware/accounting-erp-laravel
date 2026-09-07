<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReportsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class ReportResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-operational-report', 'attributes' => ['team_id' => $this->resource->team_id, 'report_key' => $this->resource->report_key, 'name' => $this->resource->name, 'category' => $this->resource->category->value, 'period_start' => $this->resource->period_start?->toDateString(), 'period_end' => $this->resource->period_end?->toDateString(), 'currency' => $this->resource->currency, 'status' => $this->resource->status->value, 'filters' => $this->resource->filters, 'summary' => $this->resource->summary, 'published_at' => $this->resource->published_at?->toISOString()], 'links' => ['self' => url('/api/v1/accounting/operational-reports/'.$this->resource->getKey())]];
    }
}
