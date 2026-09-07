<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliation\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BankReconciliation\Exceptions\InvalidReconciliation;
use Liberu\Accounting\BankReconciliation\Models\ReconciliationSession;

final class CreateReconciliationSession
{
    public function handle(array $attributes): ReconciliationSession
    {
        $start = $attributes['period_start'] ?? null;
        $end = $attributes['period_end'] ?? null;
        if (! is_numeric($attributes['bank_account_id'] ?? null) || blank($start) || blank($end) || $start > $end || ! is_numeric($attributes['opening_balance'] ?? null) || ! is_numeric($attributes['statement_balance'] ?? null)) {
            throw new InvalidReconciliation('A reconciliation requires an account, ordered period, opening balance, and statement balance.');
        }

        return DB::transaction(function () use ($attributes): ReconciliationSession {
            if (ReconciliationSession::query()->where('bank_account_id', $attributes['bank_account_id'])->where('period_start', $attributes['period_start'])->where('period_end', $attributes['period_end'])->exists()) {
                throw new InvalidReconciliation('A reconciliation already exists for this account and period.');
            }

            return ReconciliationSession::query()->create($attributes);
        });
    }
}
