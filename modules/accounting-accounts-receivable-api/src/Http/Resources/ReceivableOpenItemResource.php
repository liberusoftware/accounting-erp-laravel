<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivableApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;

class ReceivableOpenItemResource extends JsonResource
{
    public function toArray($request): array
    {
        $item = $this->resource;
        if (! $item instanceof ReceivableOpenItem) {
            return [];
        }

        return ['id' => (string) $item->id, 'type' => 'accounting-ar-open-item', 'attributes' => ['party_id' => $item->party_id, 'reference' => $item->reference, 'issued_on' => $item->issued_on?->toIso8601String(), 'due_on' => $item->due_on?->toIso8601String(), 'original_amount' => (string) $item->original_amount, 'applied_amount' => (string) $item->applied_amount, 'outstanding' => (string) number_format($item->outstanding(), 2, '.', ''), 'currency' => $item->currency, 'status' => $item->status->value, 'metadata' => $item->metadata]];
    }
}
