<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\OpeningBalances\Enums\OpeningBalanceStatus;
use Liberu\Accounting\OpeningBalances\Exceptions\InvalidOpeningBalance;
use Liberu\Accounting\OpeningBalances\Models\OpeningBalanceBatch;
use Liberu\Accounting\OpeningBalances\Models\OpeningBalanceEntry;
use Liberu\Accounting\OpeningBalances\Models\OpeningBalanceReconciliation;

final class ReconcileOpeningBalances
{
    /** @param array<int,array{entry_id:int,actual_amount:float,external_ref?:string,notes?:string}> $actuals */
    public function handle(OpeningBalanceBatch $batch, array $actuals): OpeningBalanceBatch
    {
        return DB::transaction(function () use ($batch, $actuals): OpeningBalanceBatch {/** @var OpeningBalanceBatch $lockedBatch */ $lockedBatch = OpeningBalanceBatch::query()->lockForUpdate()->findOrFail($batch->id);
            if (! in_array($lockedBatch->status, [OpeningBalanceStatus::Approved, OpeningBalanceStatus::Reconciled], true)) {
                throw new InvalidOpeningBalance('Only approved opening balances can be reconciled.');
            }$variance = false;
            foreach ($actuals as $actual) {/** @var OpeningBalanceEntry|null $entry */ $entry = $lockedBatch->entries()->find($actual['entry_id']);
                if (! $entry) {
                    throw new InvalidOpeningBalance('Reconciliation entry does not belong to this batch.');
                }$expected = $entry->netAmount();
                $actualAmount = round((float) $actual['actual_amount'], 2);
                $delta = round($expected - $actualAmount, 2);
                $variance = $variance || abs($delta) > 0.01;
                OpeningBalanceReconciliation::updateOrCreate(['batch_id' => $lockedBatch->id, 'entry_id' => $entry->id], ['expected_amount' => $expected, 'actual_amount' => $actualAmount, 'variance' => $delta, 'status' => abs($delta) > 0.01 ? 'exception' : 'matched', 'external_ref' => $actual['external_ref'] ?? null, 'notes' => $actual['notes'] ?? null]);
            }$lockedBatch->update(['status' => $variance ? OpeningBalanceStatus::Approved : OpeningBalanceStatus::Reconciled]);

            return $lockedBatch->refresh();
        });
    }
}
