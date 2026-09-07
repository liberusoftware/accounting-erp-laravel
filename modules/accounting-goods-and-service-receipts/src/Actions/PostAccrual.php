<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Actions;

use Liberu\Accounting\GoodsAndServiceReceipts\Exceptions\InvalidReceipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\Receipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\ReceiptAccrual;

final class PostAccrual
{
    public function handle(Receipt $receipt, array $a): ReceiptAccrual
    {
        $amount = (float) ($a['amount'] ?? 0);
        foreach (['accrual_ref', 'period_ref', 'source_ref'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidReceipt("Missing accrual field [{$k}].");
            }
        }if ($amount <= 0) {
            throw new InvalidReceipt('Accrual amount must be positive.');
        }

        return ReceiptAccrual::create(['receipt_id' => $receipt->getKey(), 'accrual_ref' => $a['accrual_ref'], 'amount' => $amount, 'currency' => strtoupper($a['currency'] ?? $receipt->currency), 'period_ref' => $a['period_ref'], 'status' => 'posted', 'posted_at' => now(), 'source_ref' => $a['source_ref'], 'metadata' => $a['metadata'] ?? null]);
    }
}
