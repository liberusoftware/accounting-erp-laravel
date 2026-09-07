<?php

declare(strict_types=1);

namespace Liberu\Accounting\Transfers\Actions;

use Liberu\Accounting\Transfers\Enums\TransferStatus;
use Liberu\Accounting\Transfers\Exceptions\InvalidTransfer;
use Liberu\Accounting\Transfers\Models\Transfer;

final class CompleteTransfer
{
    public function handle(Transfer $transfer): Transfer
    {
        if ($transfer->status !== TransferStatus::InTransit) {
            throw new InvalidTransfer('Only in-transit transfers can be completed.');
        }
        $transfer->update(['status' => TransferStatus::Completed]);

        return $transfer->refresh();
    }
}
