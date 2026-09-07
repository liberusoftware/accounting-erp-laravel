<?php

declare(strict_types=1);

namespace Liberu\Accounting\PeriodsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AccountingPeriodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-period', 'attributes' => ['book_id' => (string) $this->resource->book_id, 'starts_on' => $this->resource->starts_on?->toDateString(), 'ends_on' => $this->resource->ends_on?->toDateString(), 'state' => $this->resource->state->value, 'posting_ends_on' => $this->resource->posting_ends_on?->toDateString(), 'locked_by' => $this->resource->locked_by, 'locked_at' => $this->resource->locked_at?->toISOString(), 'reopened_by' => $this->resource->reopened_by, 'reopen_reason' => $this->resource->reopen_reason, 'evidence' => $this->resource->evidence, 'created_at' => $this->resource->created_at?->toISOString(), 'updated_at' => $this->resource->updated_at?->toISOString()]];
    }
}
