<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccountsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\BankAccounts\Models\BankAccount;

/** @mixin BankAccount */
final class BankAccountResource extends JsonResource
{
    public function toArray($request): array
    {
        $number = $this->account_number;
        $routing = $this->routing_number;

        return ['id' => (string) $this->id, 'type' => 'accounting-bank-account', 'attributes' => [
            'legal_entity_id' => $this->legal_entity_id, 'name' => $this->name, 'institution_name' => $this->institution_name,
            'account_type' => $this->account_type?->value, 'currency' => $this->currency, 'opening_balance' => (float) $this->opening_balance,
            'opening_date' => $this->opening_date?->toDateString(), 'current_balance' => (float) $this->current_balance,
            'masked_account_number' => $number === null ? null : str_repeat('*', max(0, strlen($number) - 4)).substr($number, -4),
            'masked_routing_number' => $routing === null ? null : str_repeat('*', max(0, strlen($routing) - 4)).substr($routing, -4),
            'feed_reference' => $this->feed_reference, 'status' => $this->status?->value, 'closed_at' => $this->closed_at?->toIso8601String(),
            'metadata' => $this->metadata,
        ], 'links' => ['self' => url('/api/v1/accounting/bank-accounts/'.$this->id)]];
    }
}
