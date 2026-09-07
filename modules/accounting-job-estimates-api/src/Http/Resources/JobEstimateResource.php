<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimatesApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\JobEstimates\Models\JobEstimate; /** @mixin JobEstimate */
final class JobEstimateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $estimate = $this->resource;

        return ['id' => $estimate->getKey(), 'estimate_ref' => $estimate->estimate_ref, 'project_ref' => $estimate->project_ref, 'title' => $estimate->title, 'currency' => $estimate->currency, 'status' => $estimate->status->value, 'version_no' => $estimate->version_no, 'total_cost' => $estimate->total_cost, 'total_revenue' => $estimate->total_revenue, 'lines' => $this->whenLoaded('lines'), 'versions' => $this->whenLoaded('versions'), 'approvals' => $this->whenLoaded('approvals'), 'actuals' => $this->whenLoaded('actuals')];
    }
}
