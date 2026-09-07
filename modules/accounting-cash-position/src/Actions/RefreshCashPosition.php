<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashPosition\Actions;

use Liberu\Accounting\CashPosition\Exceptions\InvalidCashPosition;
use Liberu\Accounting\CashPosition\Models\CashPosition;

final class RefreshCashPosition
{
    public function handle(CashPosition $position, array $balances): CashPosition
    {
        foreach (['ledger_balance', 'available_balance', 'outstanding_receipts', 'outstanding_payments', 'committed_cash'] as $field) {
            if (isset($balances[$field]) && (float) $balances[$field] < 0) {
                throw new InvalidCashPosition("{$field} cannot be negative.");
            }
        }

        $position->update([...$balances, 'refreshed_at' => now()]);

        return $position->refresh();
    }
}
