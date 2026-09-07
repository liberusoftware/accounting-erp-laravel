<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatchingApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\ThreeWayMatching\Models\MatchEvidence;
use Liberu\Accounting\ThreeWayMatching\Models\MatchException;
use Liberu\Accounting\ThreeWayMatching\Models\MatchRecord;

/** @mixin MatchRecord */
final class MatchResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-three-way-matching', 'attributes' => [
            'purchase_order_type' => $this->purchase_order_type, 'purchase_order_id' => $this->purchase_order_id, 'receipt_type' => $this->receipt_type, 'receipt_id' => $this->receipt_id, 'bill_type' => $this->bill_type, 'bill_id' => $this->bill_id, 'currency' => $this->currency, 'ordered_quantity' => (float) $this->ordered_quantity, 'received_quantity' => (float) $this->received_quantity, 'billed_quantity' => (float) $this->billed_quantity, 'ordered_unit_price' => (float) $this->ordered_unit_price, 'billed_unit_price' => (float) $this->billed_unit_price, 'expected_tax' => (float) $this->expected_tax, 'billed_tax' => (float) $this->billed_tax, 'quantity_tolerance' => (float) $this->quantity_tolerance, 'price_tolerance' => (float) $this->price_tolerance, 'tax_tolerance' => (float) $this->tax_tolerance, 'status' => $this->status->value, 'approved_by' => $this->approved_by, 'approved_at' => $this->approved_at?->toIso8601String(), 'rejected_reason' => $this->rejected_reason, 'exceptions' => $this->when($this->relationLoaded('exceptions'), fn (): array => $this->exceptions->map(fn (MatchException $exception): array => ['id' => (string) $exception->id, 'kind' => $exception->kind, 'severity' => $exception->severity->value, 'status' => $exception->status->value, 'expected_value' => $exception->expected_value === null ? null : (float) $exception->expected_value, 'actual_value' => $exception->actual_value === null ? null : (float) $exception->actual_value, 'tolerance' => $exception->tolerance === null ? null : (float) $exception->tolerance, 'resolution' => $exception->resolution])->values()->all()), 'evidence' => $this->when($this->relationLoaded('evidence'), fn (): array => $this->evidence->map(fn (MatchEvidence $evidence): array => ['id' => (string) $evidence->id, 'source_type' => $evidence->source_type, 'source_id' => $evidence->source_id])->values()->all()),
        ], 'links' => ['self' => url('/api/v1/accounting/three-way-matching/'.$this->id)]];
    }
}
