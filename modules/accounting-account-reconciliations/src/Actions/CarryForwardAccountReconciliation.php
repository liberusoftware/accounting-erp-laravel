<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliations\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountReconciliations\Enums\ReconciliationStatus;
use Liberu\Accounting\AccountReconciliations\Exceptions\InvalidAccountReconciliation;
use Liberu\Accounting\AccountReconciliations\Models\AccountReconciliation;

final class CarryForwardAccountReconciliation
{
    public function handle(AccountReconciliation $reconciliation, array $carryForward): AccountReconciliation
    {
        if ($reconciliation->status !== ReconciliationStatus::Certified || blank($carryForward['period_start'] ?? null) || blank($carryForward['period_end'] ?? null)) {
            throw new InvalidAccountReconciliation('Only certified reconciliations can be carried forward with a target period.');
        }

        return DB::transaction(function () use ($reconciliation, $carryForward): AccountReconciliation {
            $reconciliation->update(['carry_forward' => $carryForward, 'status' => ReconciliationStatus::CarriedForward]);

            return $reconciliation;
        });
    }
}
