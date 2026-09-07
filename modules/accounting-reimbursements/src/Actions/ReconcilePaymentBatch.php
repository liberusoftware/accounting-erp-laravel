<?php

declare(strict_types=1);

namespace Liberu\Accounting\Reimbursements\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Reimbursements\Enums\BatchStatus;
use Liberu\Accounting\Reimbursements\Exceptions\InvalidReimbursement;
use Liberu\Accounting\Reimbursements\Models\ReimbursementBatch;
use Liberu\Accounting\Reimbursements\Models\ReimbursementReconciliation;

final class ReconcilePaymentBatch
{
    public function handle(ReimbursementBatch $batch, float $settledAmount, ?string $externalRef = null): ReimbursementReconciliation
    {
        $expected = round((float) $batch->total_amount, 2);
        $settled = round($settledAmount, 2);
        $variance = round($expected - $settled, 2);
        if (abs($variance) > 0.01) {
            throw new InvalidReimbursement('Settled amount does not reconcile with the payment batch.');
        }

        return DB::transaction(function () use ($batch, $expected, $settled, $variance, $externalRef): ReimbursementReconciliation {
            $batch->update(['status' => BatchStatus::Reconciled]);

            return ReimbursementReconciliation::create(['batch_id' => $batch->id, 'expected_amount' => $expected, 'settled_amount' => $settled, 'variance' => $variance, 'status' => 'matched', 'external_ref' => $externalRef]);
        });
    }
}
