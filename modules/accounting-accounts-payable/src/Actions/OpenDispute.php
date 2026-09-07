<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsPayable\Enums\DisputeStatus;
use Liberu\Accounting\AccountsPayable\Enums\PayableStatus;
use Liberu\Accounting\AccountsPayable\Events\DisputeOpened;
use Liberu\Accounting\AccountsPayable\Exceptions\InvalidPayable;
use Liberu\Accounting\AccountsPayable\Models\PayableDispute;
use Liberu\Accounting\AccountsPayable\Models\PayableOpenItem;

final class OpenDispute
{
    public function handle(PayableOpenItem $item, string $reason, ?float $amount = null): PayableDispute
    {
        $dispute = DB::transaction(function () use ($item, $amount, $reason): PayableDispute {
            $item = PayableOpenItem::query()->lockForUpdate()->findOrFail($item->getKey());
            $amount ??= $item->outstanding();
            if (blank($reason) || $amount <= 0 || $amount > $item->outstanding() || $item->status === PayableStatus::Settled) {
                throw new InvalidPayable('A dispute requires a valid reason and amount within the open balance.');
            }
            if ($item->disputes()->where('status', DisputeStatus::Open)->exists()) {
                throw new InvalidPayable('An open dispute already exists for this item.');
            }
            $dispute = $item->disputes()->create(['amount' => $amount, 'reason' => $reason, 'status' => DisputeStatus::Open, 'opened_at' => now()]);
            $item->update(['status' => PayableStatus::Disputed]);
            DB::afterCommit(fn (): mixed => event(new DisputeOpened(PayableDispute::query()->findOrFail($dispute->getKey()))));

            return $dispute;
        });

        return $dispute;
    }
}
