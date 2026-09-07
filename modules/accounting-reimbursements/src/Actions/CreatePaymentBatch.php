<?php

declare(strict_types=1);

namespace Liberu\Accounting\Reimbursements\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Reimbursements\Enums\BatchStatus;
use Liberu\Accounting\Reimbursements\Enums\ReimbursementStatus;
use Liberu\Accounting\Reimbursements\Exceptions\InvalidReimbursement;
use Liberu\Accounting\Reimbursements\Models\ReimbursementBatch;
use Liberu\Accounting\Reimbursements\Models\ReimbursementLiability;

final class CreatePaymentBatch
{
    public function handle(array $liabilityIds, ?int $teamId = null): ReimbursementBatch
    {
        return DB::transaction(function () use ($liabilityIds, $teamId): ReimbursementBatch {
            $liabilities = ReimbursementLiability::query()->whereIn('id', $liabilityIds)->when($teamId !== null, fn ($query) => $query->where('team_id', $teamId))->lockForUpdate()->get();
            if ($liabilities->isEmpty() || $liabilities->contains(fn (ReimbursementLiability $liability): bool => $liability->status !== ReimbursementStatus::Approved)) {
                throw new InvalidReimbursement('Only approved liabilities can be batched.');
            }$currencies = $liabilities->pluck('currency')->unique();
            if ($currencies->count() !== 1) {
                throw new InvalidReimbursement('A payment batch cannot mix currencies.');
            }$batch = ReimbursementBatch::create(['team_id' => $liabilities->first()->team_id, 'currency' => $currencies->first(), 'total_amount' => $liabilities->sum(fn (ReimbursementLiability $liability): float => (float) $liability->amount), 'status' => BatchStatus::Draft]);
            $liabilities->each(fn (ReimbursementLiability $liability) => $liability->update(['batch_id' => $batch->id, 'status' => ReimbursementStatus::Batched]));

            return $batch->load('liabilities');
        });
    }
}
