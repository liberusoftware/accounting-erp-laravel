<?php

declare(strict_types=1);

namespace Liberu\Accounting\MileageApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\Mileage\Models\MileageTrip;

/** @mixin MileageTrip */
final class MileageResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-mileage', 'attributes' => ['trip_ref' => $this->resource->trip_ref, 'employee_ref' => $this->resource->employee_ref, 'project_ref' => $this->resource->project_ref, 'origin' => $this->resource->origin, 'destination' => $this->resource->destination, 'trip_date' => $this->resource->trip_date?->toDateString(), 'distance' => (float) $this->resource->distance, 'distance_unit' => $this->resource->distance_unit, 'business_purpose' => $this->resource->business_purpose, 'region' => $this->resource->region, 'currency' => $this->resource->currency, 'reimbursement_amount' => (float) $this->resource->reimbursement_amount, 'status' => $this->resource->status?->value, 'source' => $this->resource->source, 'created_at' => $this->resource->created_at?->toISOString(), 'updated_at' => $this->resource->updated_at?->toISOString()]];
    }
}
