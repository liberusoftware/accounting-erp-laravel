<?php

declare(strict_types=1);

namespace Liberu\Accounting\LeasesApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\Leases\Models\Lease; /** @mixin Lease */
final class LeaseResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-lease', 'attributes' => ['lease_ref' => $this->resource->lease_ref, 'name' => $this->resource->name, 'lessor_ref' => $this->resource->lessor_ref, 'asset_ref' => $this->resource->asset_ref, 'commencement_date' => $this->resource->commencement_date?->toDateString(), 'end_date' => $this->resource->end_date?->toDateString(), 'currency' => $this->resource->currency, 'payment_amount' => (float) $this->resource->payment_amount, 'payment_frequency' => $this->resource->payment_frequency, 'status' => $this->resource->status?->value, 'right_of_use_asset' => (float) $this->resource->right_of_use_asset, 'lease_liability' => (float) $this->resource->lease_liability, 'accumulated_depreciation' => (float) $this->resource->accumulated_depreciation]];
    }
}
