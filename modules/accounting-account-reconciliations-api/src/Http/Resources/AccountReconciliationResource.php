<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliationsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class AccountReconciliationResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-account-reconciliations', 'attributes' => ['team_id' => $this->team_id, 'book_id' => $this->book_id, 'account_id' => $this->account_id, 'period_start' => $this->period_start?->toDateString(), 'period_end' => $this->period_end?->toDateString(), 'status' => $this->status?->value, 'template' => $this->template, 'source_balance' => $this->source_balance, 'supporting_items' => $this->supporting_items, 'preparer' => $this->preparer, 'reviewer' => $this->reviewer, 'aging' => $this->aging, 'certification' => $this->certification, 'carry_forward' => $this->carry_forward], 'links' => ['self' => url('/api/v1/accounting/account-reconciliations/'.$this->resource->getKey())]];
    }
}
