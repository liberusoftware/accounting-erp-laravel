<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPaymentsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\BillPayments\Models\BillPaymentProposal;

/** @mixin BillPaymentProposal */
final class BillPaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-bill-payment', 'attributes' => ['supplier_id' => $this->supplier_id, 'bill_reference' => $this->bill_reference, 'amount' => (float) $this->amount, 'currency' => $this->currency, 'due_date' => $this->due_date?->toDateString(), 'discount_date' => $this->discount_date?->toDateString(), 'discount_rate' => (float) $this->discount_rate, 'provider' => $this->provider, 'status' => $this->status?->value, 'provider_reference' => $this->provider_reference, 'remittance_reference' => $this->remittance_reference, 'approved_at' => $this->approved_at?->toIso8601String(), 'submitted_at' => $this->submitted_at?->toIso8601String(), 'paid_at' => $this->paid_at?->toIso8601String()]];
    }
}
