<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilitiesApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PayrollLiabilityResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'liability_ref' => $this->liability_ref,
            'agency_ref' => $this->agency_ref,
            'payee_ref' => $this->payee_ref,
            'currency' => $this->currency,
            'amount' => $this->amount,
            'paid_amount' => $this->paid_amount,
            'outstanding' => $this->outstanding(),
            'due_on' => $this->due_on?->toDateString(),
            'status' => $this->status?->value,
            'payment_ref' => $this->payment_ref,
            'allocation_ref' => $this->allocation_ref,
            'reconciliation_ref' => $this->reconciliation_ref,
            'reconciled_at' => $this->reconciled_at?->toISOString(),
            'metadata' => $this->metadata,
        ];
    }
}
