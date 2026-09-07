<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitionsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PurchaseRequisitionResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'requester_ref' => $this->requester_ref,
            'title' => $this->title,
            'currency' => $this->currency,
            'total_amount' => $this->total_amount,
            'lines' => $this->lines,
            'coding' => $this->coding,
            'budget' => $this->budget,
            'attachments' => $this->attachments,
            'status' => $this->status?->value,
            'submitted_at' => $this->submitted_at?->toISOString(),
            'approved_at' => $this->approved_at?->toISOString(),
            'sourcing_ref' => $this->sourcing_ref,
            'converted_ref' => $this->converted_ref,
            'metadata' => $this->metadata,
            'approvals' => $this->whenLoaded('approvals'),
        ];
    }
}
