<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortalApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\SupplierPortal\Models\PortalResource;

/** @mixin PortalResource */
final class PortalResourceResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-supplier-portal', 'attributes' => ['supplier_id' => $this->supplier_id, 'resource_type' => $this->type->value, 'reference' => $this->reference, 'status' => $this->status->value, 'currency' => $this->currency, 'amount' => (float) $this->amount, 'payload' => $this->payload, 'submitted_at' => $this->submitted_at?->toIso8601String(), 'approved_at' => $this->approved_at?->toIso8601String(), 'rejected_reason' => $this->rejected_reason], 'links' => ['self' => url('/api/v1/accounting/supplier-portal/'.$this->id)]];
    }
}
