<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReportingApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\ManagementReporting\Models\ReportPack; /** @mixin ReportPack */
final class ReportPackResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-management-report-pack', 'attributes' => ['report_ref' => $this->resource->report_ref, 'name' => $this->resource->name, 'period_start' => $this->resource->period_start?->toDateString(), 'period_end' => $this->resource->period_end?->toDateString(), 'currency' => $this->resource->currency, 'status' => $this->resource->status?->value, 'version' => $this->resource->version, 'approved_at' => $this->resource->approved_at?->toISOString(), 'delivered_at' => $this->resource->delivered_at?->toISOString(), 'archived_at' => $this->resource->archived_at?->toISOString()]];
    }
}
