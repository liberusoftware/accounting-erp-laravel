<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccounts\Queries;

use Liberu\Accounting\BankAccounts\Models\BankAccount;

final class BankAccountBalanceQuery
{
    public function handle(?int $legalEntityId = null): array
    {
        $accounts = BankAccount::query()->where('status', '!=', 'closed')->when($legalEntityId, fn ($query) => $query->where('legal_entity_id', $legalEntityId))->orderBy('name')->get();

        return ['total' => (float) $accounts->sum('current_balance'), 'accounts' => $accounts];
    }
}
