<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\AccountsPayable\Models\PayableDispute;

final class PayableDisputeResource extends JsonResource
{
    public function toArray($request): array
    {
        $dispute = $this->resource;
        if (! $dispute instanceof PayableDispute) {
            return [];
        }

        return ['id' => (string) $dispute->id, 'type' => 'accounting-ap-dispute', 'attributes' => ['open_item_id' => $dispute->open_item_id, 'amount' => (string) $dispute->amount, 'reason' => $dispute->reason, 'status' => $dispute->status?->value, 'opened_at' => $dispute->opened_at?->toIso8601String(), 'resolved_at' => $dispute->resolved_at?->toIso8601String(), 'resolution' => $dispute->resolution, 'metadata' => $dispute->metadata]];
    }
}
