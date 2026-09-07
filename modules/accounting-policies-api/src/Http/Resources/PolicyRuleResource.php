<?php

namespace Liberu\Accounting\PoliciesApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PolicyRuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-policy-rule', 'attributes' => ['book_id' => (string) $this->resource->book_id, 'category' => $this->resource->category->value, 'key' => $this->resource->key, 'value' => $this->resource->value, 'effective_from' => $this->resource->effective_from?->toDateString(), 'effective_until' => $this->resource->effective_until?->toDateString(), 'is_active' => $this->resource->is_active, 'approved_by' => $this->resource->approved_by, 'approved_at' => $this->resource->approved_at?->toISOString(), 'metadata' => $this->resource->metadata]];
    }
}
