<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashPosition\Actions;

use Liberu\Accounting\CashPosition\Exceptions\InvalidCashPosition;
use Liberu\Accounting\CashPosition\Models\CashPosition;

final class CreateCashPosition
{
    public function handle(array $attributes): CashPosition
    {
        foreach (['team_id', 'view_ref', 'currency'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidCashPosition("{$field} is required.");
            }
        }

        if (! preg_match('/^[A-Z]{3}$/', (string) $attributes['currency'])) {
            throw new InvalidCashPosition('Currency must be a three-letter uppercase code.');
        }

        foreach (['ledger_balance', 'available_balance', 'outstanding_receipts', 'outstanding_payments', 'committed_cash'] as $field) {
            if ((float) ($attributes[$field] ?? 0) < 0) {
                throw new InvalidCashPosition("{$field} cannot be negative.");
            }
        }

        return CashPosition::create([...$attributes, 'refreshed_at' => now()]);
    }
}
