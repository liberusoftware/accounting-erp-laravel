<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsReceivable\Enums\DisputeStatus;
use Liberu\Accounting\AccountsReceivable\Enums\ReceivableStatus;
use Liberu\Accounting\AccountsReceivable\Events\DisputeResolved;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableDispute;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;

final class ResolveDispute
{
    public function handle(ReceivableDispute $dispute, string $resolution, bool $accepted = false): ReceivableDispute
    {
        if ($dispute->status !== DisputeStatus::Open || blank($resolution)) {
            throw new \InvalidArgumentException('Only open disputes can be resolved with a resolution.');
        }

        return DB::transaction(function () use ($dispute, $resolution, $accepted): ReceivableDispute {
            $dispute->update(['status' => $accepted ? DisputeStatus::Resolved : DisputeStatus::Rejected, 'resolution' => $resolution, 'resolved_at' => now()]);
            $openItem = ReceivableOpenItem::query()->lockForUpdate()->findOrFail($dispute->open_item_id);
            if ($openItem->status === ReceivableStatus::Disputed) {
                $openItem->update(['status' => $openItem->outstanding() <= 0 ? ReceivableStatus::Settled : ReceivableStatus::Open]);
            }
            $dispute = ReceivableDispute::query()->findOrFail($dispute->getKey());
            DB::afterCommit(fn (): mixed => event(new DisputeResolved(ReceivableDispute::query()->findOrFail($dispute->getKey()))));

            return $dispute;
        });
    }
}
