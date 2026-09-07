<?php

declare(strict_types=1);

namespace Liberu\Accounting\Transfers\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Transfers\Enums\TransferStatus;
use Liberu\Accounting\Transfers\Models\Transfer;
use Liberu\Accounting\Transfers\Models\TransferReconciliation;

final class ReconcileTransfer
{
    public function handle(Transfer $transfer, array $attributes): TransferReconciliation
    {
        return DB::transaction(function () use ($transfer, $attributes): TransferReconciliation {
            $reconciliation = TransferReconciliation::create(array_merge($attributes, ['team_id' => $transfer->team_id, 'transfer_id' => $transfer->id, 'status' => 'matched']));
            $transfer->update(['status' => TransferStatus::Reconciled]);

            return $reconciliation;
        });
    }
}
