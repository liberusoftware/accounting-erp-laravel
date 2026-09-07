<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\AccountsPayable\Enums\DisputeStatus;
use Liberu\Accounting\AccountsPayable\Enums\PayableStatus;
use Liberu\Accounting\AccountsPayable\Events\DisputeResolved;
use Liberu\Accounting\AccountsPayable\Models\PayableDispute;
use Liberu\Accounting\AccountsPayable\Models\PayableOpenItem;

final class ResolveDispute
{
    public function handle(PayableDispute $dispute, string $resolution, bool $accepted = false): PayableDispute
    {
        return DB::transaction(function () use ($dispute, $resolution, $accepted): PayableDispute {
            $dispute = PayableDispute::query()->lockForUpdate()->findOrFail($dispute->getKey());
            if ($dispute->status !== DisputeStatus::Open || blank($resolution)) {
                throw new \InvalidArgumentException('Only open disputes can be resolved with a resolution.');
            }
            $dispute->update(['status' => $accepted ? DisputeStatus::Resolved : DisputeStatus::Rejected, 'resolution' => $resolution, 'resolved_at' => now()]);
            $item = PayableOpenItem::query()->findOrFail($dispute->open_item_id);
            if ($item->status === PayableStatus::Disputed) {
                $item->update(['status' => $item->outstanding() <= 0 ? PayableStatus::Settled : PayableStatus::Open]);
            }
            $dispute = PayableDispute::query()->findOrFail($dispute->getKey());
            DB::afterCommit(fn (): mixed => event(new DisputeResolved(PayableDispute::query()->findOrFail($dispute->getKey()))));

            return $dispute->load('openItem');
        });
    }
}
