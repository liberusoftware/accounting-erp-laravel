<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivableApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableReceipt;

final class ReceivableReceiptResource extends JsonResource
{
    public function toArray($request): array
    {
        $receipt = $this->resource;
        if (! $receipt instanceof ReceivableReceipt) {
            return [];
        }

        return ['id' => (string) $receipt->id, 'type' => 'accounting-ar-receipt', 'attributes' => ['party_id' => $receipt->party_id, 'received_on' => $receipt->received_on?->toDateString(), 'amount' => (string) $receipt->amount, 'applied_amount' => (string) $receipt->applied_amount, 'unapplied' => (string) number_format($receipt->unapplied(), 2, '.', ''), 'currency' => $receipt->currency, 'reference' => $receipt->reference, 'status' => $receipt->status->value, 'metadata' => $receipt->metadata]];
    }
}
