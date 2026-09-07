<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrdersApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PurchaseOrderResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'supplier_ref' => $this->supplier_ref,
            'order_number' => $this->order_number,
            'currency' => $this->currency,
            'order_date' => $this->order_date?->toDateString(),
            'expected_delivery_on' => $this->expected_delivery_on?->toDateString(),
            'total_amount' => $this->total_amount,
            'status' => $this->status?->value,
            'commitment_ref' => $this->commitment_ref,
            'source_requisition_ref' => $this->source_requisition_ref,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'lines' => $this->whenLoaded('lines'),
            'receipts' => $this->whenLoaded('receipts'),
            'changes' => $this->whenLoaded('changes'),
        ];
    }
}
