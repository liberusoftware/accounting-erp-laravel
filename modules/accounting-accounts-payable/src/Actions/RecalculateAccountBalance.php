<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Actions;

use Liberu\Accounting\AccountsPayable\Models\PayableAccount;
use Liberu\Accounting\AccountsPayable\Models\PayableOpenItem;

final class RecalculateAccountBalance
{
    public function handle(int $partyId): PayableAccount
    {
        $account = app(EnsureAccount::class)->handle($partyId);
        $account->update(['current_balance' => PayableOpenItem::query()->where('party_id', $partyId)->where('status', '!=', 'settled')->get()->sum(fn (PayableOpenItem $item): float => $item->outstanding())]);

        return $account->refresh();
    }
}
