<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Actions;

use Liberu\Accounting\AccountsReceivable\Models\ReceivableAccount;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;

final class RecalculateAccountBalance
{
    public function handle(int $partyId): ReceivableAccount
    {
        $account = app(EnsureAccount::class)->handle($partyId);
        $balance = (float) ReceivableOpenItem::query()
            ->where('party_id', $partyId)
            ->get()
            ->sum(fn (ReceivableOpenItem $item): float => $item->outstanding());

        $account->update(['current_balance' => $balance]);

        return $account->refresh();
    }
}
