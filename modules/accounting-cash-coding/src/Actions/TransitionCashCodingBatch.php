<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCoding\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\CashCoding\Enums\CashCodingStatus;
use Liberu\Accounting\CashCoding\Exceptions\InvalidCashCoding;
use Liberu\Accounting\CashCoding\Models\CashCodingBatch;

final class TransitionCashCodingBatch
{
    public function review(CashCodingBatch $batch, ?int $actor = null): CashCodingBatch
    {
        if ($batch->status !== CashCodingStatus::Draft) {
            throw new InvalidCashCoding('Only draft coding batches can enter review.');
        }

        return DB::transaction(fn (): CashCodingBatch => tap($batch)->update(['status' => CashCodingStatus::InReview, 'reviewed_by' => $actor]))->refresh();
    }

    public function post(CashCodingBatch $batch, ?int $actor = null): CashCodingBatch
    {
        if ($batch->status === CashCodingStatus::Posted) {
            return $batch;
        }
        if ($batch->status !== CashCodingStatus::InReview) {
            throw new InvalidCashCoding('Only reviewed coding batches can be posted.');
        }

        return DB::transaction(fn (): CashCodingBatch => tap($batch)->update(['status' => CashCodingStatus::Posted, 'posted_by' => $actor, 'posted_at' => now()]))->refresh();
    }

    public function undo(CashCodingBatch $batch, string $reason): CashCodingBatch
    {
        if ($batch->status !== CashCodingStatus::Posted || blank(trim($reason))) {
            throw new InvalidCashCoding('Only posted batches can be undone and an undo reason is required.');
        }

        return DB::transaction(fn (): CashCodingBatch => tap($batch)->update(['status' => CashCodingStatus::Undone, 'undo_reason' => trim($reason), 'undone_at' => now()]))->refresh();
    }
}
