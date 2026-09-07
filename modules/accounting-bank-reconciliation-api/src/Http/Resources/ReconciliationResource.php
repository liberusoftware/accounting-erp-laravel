<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliationApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\BankReconciliation\Models\ReconciliationSession;

/** @mixin ReconciliationSession */
final class ReconciliationResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-bank-reconciliation', 'attributes' => ['bank_account_id' => $this->bank_account_id, 'period_start' => $this->period_start?->toDateString(), 'period_end' => $this->period_end?->toDateString(), 'opening_balance' => (float) $this->opening_balance, 'statement_balance' => (float) $this->statement_balance, 'status' => $this->status?->value, 'signed_off_at' => $this->signed_off_at?->toIso8601String()], 'links' => ['self' => url('/api/v1/accounting/bank-reconciliation/'.$this->id)]];
    }
}
