<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayableOpenItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => $this->id, 'party_id' => $this->party_id, 'reference' => $this->reference, 'issued_on' => $this->issued_on?->toDateString(), 'due_on' => $this->due_on?->toDateString(), 'original_amount' => (float) $this->original_amount, 'paid_amount' => (float) $this->paid_amount, 'outstanding_amount' => $this->outstanding(), 'currency' => $this->currency, 'status' => $this->status?->value, 'payment_terms' => $this->payment_terms];
    }
}
