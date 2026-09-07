<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliation\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BankReconciliation\Enums\ReconciliationEntryStatus;
use Liberu\Accounting\BankReconciliation\Events\ReconciliationEntryConfirmed;
use Liberu\Accounting\BankReconciliation\Exceptions\InvalidReconciliation;
use Liberu\Accounting\BankReconciliation\Models\ReconciliationEntry;

final class ConfirmReconciliationEntry
{
    public function handle(ReconciliationEntry $entry): ReconciliationEntry
    {
        if ($entry->status === ReconciliationEntryStatus::Confirmed) {
            return $entry;
        }
        if ($entry->session()->value('status') === 'signed_off') {
            throw new InvalidReconciliation('Signed-off sessions cannot change entries.');
        }

        return DB::transaction(function () use ($entry): ReconciliationEntry {
            $entry->update(['status' => ReconciliationEntryStatus::Confirmed, 'confirmed_at' => now(), 'confirmed_by' => auth()->id()]);
            DB::afterCommit(fn (): mixed => event(new ReconciliationEntryConfirmed($entry->refresh())));

            return $entry->refresh();
        });
    }
}
