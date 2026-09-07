<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Intercompany\Enums\TransactionStatus;
use Liberu\Accounting\Intercompany\Exceptions\InvalidIntercompany;
use Liberu\Accounting\Intercompany\Models\IntercompanyTransaction;

final class SettleTransaction
{
    public function handle(IntercompanyTransaction $transaction, array $a): IntercompanyTransaction
    {
        $amount = (float) ($a['amount'] ?? 0);
        if ($transaction->status !== TransactionStatus::Confirmed) {
            throw new InvalidIntercompany('Only confirmed transactions can be settled.');
        }if ($amount <= 0 || $amount > (float) $transaction->amount || blank($a['settlement_ref'] ?? null) || blank($a['source_ref'] ?? null)) {
            throw new InvalidIntercompany('Settlement amount or references are invalid.');
        }

        return DB::transaction(function () use ($transaction, $a, $amount): IntercompanyTransaction {
            $transaction->settlements()->create(['settlement_ref' => $a['settlement_ref'], 'amount' => $amount, 'currency' => strtoupper($a['currency'] ?? $transaction->currency), 'settled_at' => $a['settled_at'] ?? now(), 'source_ref' => $a['source_ref']]);
            $transaction->update(['status' => TransactionStatus::Settled]);

            return $transaction->refresh();
        });
    }
}
