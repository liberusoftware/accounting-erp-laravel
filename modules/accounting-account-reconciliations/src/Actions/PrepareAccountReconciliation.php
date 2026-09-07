<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliations\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountReconciliations\Enums\ReconciliationStatus;
use Liberu\Accounting\AccountReconciliations\Exceptions\InvalidAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Models\AccountReconciliation;

final class PrepareAccountReconciliation
{
    public function handle(AccountReconciliation $reconciliation, array $preparer, array $sourceBalance, array $supportingItems = []): AccountReconciliation
    {
        if (blank($preparer['user_id'] ?? null) || ! array_key_exists('amount', $sourceBalance)) {
            throw new InvalidAccountReconciliation('A preparer and source balance are required.');
        }

        return DB::transaction(function () use ($reconciliation, $preparer, $sourceBalance, $supportingItems): AccountReconciliation {
            $reconciliation->update(['preparer' => $preparer, 'source_balance' => $sourceBalance, 'supporting_items' => $supportingItems, 'status' => ReconciliationStatus::Prepared]);

            return $reconciliation;
        });
    }
}
