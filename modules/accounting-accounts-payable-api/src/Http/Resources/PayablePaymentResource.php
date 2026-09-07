<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\AccountsPayable\Models\PayablePayment;

final class PayablePaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        $payment = $this->resource;
        if (! $payment instanceof PayablePayment) {
            return [];
        }

        return ['id' => (string) $payment->id, 'type' => 'accounting-ap-payment', 'attributes' => ['party_id' => $payment->party_id, 'paid_on' => $payment->paid_on?->toDateString(), 'amount' => (string) $payment->amount, 'applied_amount' => (string) $payment->applied_amount, 'unapplied' => (string) number_format($payment->unapplied(), 2, '.', ''), 'currency' => $payment->currency, 'reference' => $payment->reference, 'status' => $payment->status?->value, 'metadata' => $payment->metadata]];
    }
}
