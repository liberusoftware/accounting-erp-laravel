<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\OpeningBalances\Enums\OpeningBalanceStatus;
use Liberu\Accounting\OpeningBalances\Events\OpeningBalancesApproved;
use Liberu\Accounting\OpeningBalances\Exceptions\InvalidOpeningBalance;
use Liberu\Accounting\OpeningBalances\Models\OpeningBalanceBatch;

final class ApproveOpeningBalances
{
    public function handle(OpeningBalanceBatch $batch, int $actorId): OpeningBalanceBatch
    {
        return DB::transaction(function () use ($batch, $actorId): OpeningBalanceBatch {/** @var OpeningBalanceBatch $lockedBatch */ $lockedBatch = OpeningBalanceBatch::query()->lockForUpdate()->findOrFail($batch->id);
            if ($lockedBatch->status !== OpeningBalanceStatus::Validated) {
                throw new InvalidOpeningBalance('Only validated opening balances can be approved.');
            }$lockedBatch->update(['status' => OpeningBalanceStatus::Approved, 'approved_by' => $actorId, 'approved_at' => now()]);
            $lockedBatch->audits()->create(['event_type' => 'opening_balances_approved', 'actor_id' => $actorId, 'payload' => ['batch_id' => $lockedBatch->id], 'payload_hash' => hash('sha256', (string) $lockedBatch->id.':approved'), 'created_at' => now()]);
            $result = $lockedBatch->refresh();
            DB::afterCommit(fn () => event(new OpeningBalancesApproved($result)));

            return $result;
        });
    }
}
