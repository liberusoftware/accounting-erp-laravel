<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsReceivable\Enums\ReceivableStatus;
use Liberu\Accounting\AccountsReceivable\Events\ReceiptApplied;
use Liberu\Accounting\AccountsReceivable\Exceptions\InvalidReceivable;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableReceipt;

final class ApplyReceipt
{
    public function handle(ReceivableReceipt $receipt, ReceivableOpenItem $item, float $amount): ReceivableReceipt
    {
        return DB::transaction(function () use ($receipt, $item, $amount) {
            $receipt = ReceivableReceipt::query()->lockForUpdate()->findOrFail($receipt->id);
            $item = ReceivableOpenItem::query()->lockForUpdate()->findOrFail($item->id);
            if ($amount <= 0 || ($receipt->party_id !== null && $receipt->party_id !== $item->party_id) || $receipt->currency !== $item->currency || $amount > $receipt->unapplied() || $amount > $item->outstanding()) {
                throw new InvalidReceivable('Receipt application must match the customer and remain within both outstanding balances.');
            }
            if ($receipt->applications()->where('open_item_id', $item->id)->exists()) {
                throw new InvalidReceivable('A receipt can only be applied to the same open item once.');
            }
            $receipt->applications()->create(['open_item_id' => $item->id, 'amount' => $amount]);
            $receipt->applied_amount = (float) $receipt->applied_amount + $amount;
            $receipt->status = $receipt->unapplied() <= 0 ? ReceivableStatus::Applied : ReceivableStatus::Partial;
            $receipt->save();
            $item->applied_amount = (float) $item->applied_amount + $amount;
            $item->status = $item->outstanding() <= 0 ? ReceivableStatus::Settled : ReceivableStatus::Partial;
            $item->save();
            app(RecalculateAccountBalance::class)->handle((int) $item->party_id);
            DB::afterCommit(fn () => event(new ReceiptApplied($receipt->fresh(), $item->fresh(), $amount)));

            return $receipt->refresh()->load('applications');
        });
    }
}
