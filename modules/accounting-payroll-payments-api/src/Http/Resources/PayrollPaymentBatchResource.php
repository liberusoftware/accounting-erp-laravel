<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPaymentsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PayrollPaymentBatchResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->resource->getKey(),
            'type' => 'accounting-payroll-payment-batch',
            'attributes' => [
                'team_id' => (int) $this->resource->team_id,
                'batch_ref' => $this->resource->batch_ref,
                'net_pay_ref' => $this->resource->net_pay_ref,
                'liability_ref' => $this->resource->liability_ref,
                'currency' => $this->resource->currency,
                'net_pay_amount' => (string) $this->resource->net_pay_amount,
                'liability_amount' => (string) $this->resource->liability_amount,
                'total_amount' => number_format($this->resource->totalAmount(), 2, '.', ''),
                'status' => $this->resource->status?->value,
                'provider' => $this->resource->provider,
                'provider_payment_ref' => $this->resource->provider_payment_ref,
                'failure_code' => $this->resource->failure_code,
                'failure_message' => $this->resource->failure_message,
                'approved_at' => $this->resource->approved_at?->toISOString(),
                'submitted_at' => $this->resource->submitted_at?->toISOString(),
                'settled_at' => $this->resource->settled_at?->toISOString(),
                'reconciled_at' => $this->resource->reconciled_at?->toISOString(),
                'reconciliation_ref' => $this->resource->reconciliation_ref,
                'metadata' => $this->resource->metadata,
            ],
        ];
    }
}
