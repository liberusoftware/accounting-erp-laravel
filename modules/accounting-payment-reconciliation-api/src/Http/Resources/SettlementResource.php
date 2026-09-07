<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliationApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class SettlementResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-payment-reconciliation', 'attributes' => ['team_id' => $this->resource->team_id, 'provider' => $this->resource->provider, 'merchant_ref' => $this->resource->merchant_ref, 'settlement_ref' => $this->resource->settlement_ref, 'period_start' => $this->resource->period_start?->toDateString(), 'period_end' => $this->resource->period_end?->toDateString(), 'currency' => $this->resource->currency, 'gross_amount' => (float) $this->resource->gross_amount, 'fee_amount' => (float) $this->resource->fee_amount, 'refund_amount' => (float) $this->resource->refund_amount, 'dispute_amount' => (float) $this->resource->dispute_amount, 'net_amount' => (float) $this->resource->net_amount, 'status' => $this->resource->status->value, 'created_at' => $this->resource->created_at?->toISOString()], 'links' => ['self' => url('/api/v1/accounting/payment-reconciliation/'.$this->resource->getKey())]];
    }
}
