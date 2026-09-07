<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoans\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\DebtAndLoans\Enums\DebtMovementStatus;
use Liberu\Accounting\DebtAndLoans\Exceptions\InvalidDebt;
use Liberu\Accounting\DebtAndLoans\Models\DebtMovement;

final class ReconcileDebtMovement
{
    public function handle(DebtMovement $movement, string $journalReference): DebtMovement
    {
        if ($movement->status !== DebtMovementStatus::Scheduled || blank($journalReference)) {
            throw new InvalidDebt('Only scheduled movements with a journal reference can be reconciled.');
        }

        return DB::transaction(function () use ($movement, $journalReference): DebtMovement {
            $movement->update(['status' => DebtMovementStatus::Reconciled, 'journal_ref' => $journalReference]);

            return $movement->fresh('facility');
        });
    }
}
