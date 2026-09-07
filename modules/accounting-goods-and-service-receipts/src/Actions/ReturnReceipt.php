<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\GoodsAndServiceReceipts\Enums\ReceiptStatus;
use Liberu\Accounting\GoodsAndServiceReceipts\Exceptions\InvalidReceipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\Receipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\ReceiptReturn;

final class ReturnReceipt
{
    public function handle(Receipt $receipt, array $a): ReceiptReturn
    {
        $qty = (float) ($a['quantity'] ?? 0);
        $value = (float) ($a['value'] ?? 0);
        foreach (['return_ref', 'line_ref', 'reason', 'source_ref'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidReceipt("Missing return field [{$k}].");
            }
        }$line = $receipt->lines()->where('line_ref', $a['line_ref'])->first();
        if (! $line || $qty <= 0 || $qty > (float) $line->received_quantity - (float) $line->returned_quantity || $value < 0) {
            throw new InvalidReceipt('Return exceeds the received quantity or is invalid.');
        }

        return DB::transaction(function () use ($receipt, $line, $a, $qty, $value): ReceiptReturn {
            $r = ReceiptReturn::create(['receipt_id' => $receipt->getKey(), 'return_ref' => $a['return_ref'], 'line_ref' => $a['line_ref'], 'quantity' => $qty, 'value' => $value, 'reason' => $a['reason'], 'source_ref' => $a['source_ref'], 'returned_at' => now()]);
            $line->increment('returned_quantity', $qty);
            $receipt->update(['status' => ReceiptStatus::PartiallyReturned]);

            return $r;
        });
    }
}
