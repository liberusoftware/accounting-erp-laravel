<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Actions;

use Liberu\Accounting\GoodsAndServiceReceipts\Enums\ReceiptStatus;
use Liberu\Accounting\GoodsAndServiceReceipts\Enums\ReceiptType;
use Liberu\Accounting\GoodsAndServiceReceipts\Exceptions\InvalidReceipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\Receipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\ServiceConfirmation;

final class ConfirmService
{
    public function handle(Receipt $receipt, array $a): ServiceConfirmation
    {
        if ($receipt->receipt_type !== ReceiptType::Service || $receipt->status === ReceiptStatus::Closed) {
            throw new InvalidReceipt('Only open service receipts can be confirmed.');
        }$quantity = (float) ($a['quantity'] ?? 0);
        $value = (float) ($a['value'] ?? 0);
        foreach (['confirmation_ref', 'service_period', 'confirmed_by'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidReceipt("Missing confirmation field [{$k}].");
            }
        }if ($quantity <= 0 || $value < 0) {
            throw new InvalidReceipt('Service confirmation quantities and value are invalid.');
        }$c = ServiceConfirmation::create(['receipt_id' => $receipt->getKey(), 'confirmation_ref' => $a['confirmation_ref'], 'service_period' => $a['service_period'], 'quantity' => $quantity, 'value' => $value, 'confirmed_by' => $a['confirmed_by'], 'confirmed_at' => now(), 'comment' => $a['comment'] ?? null]);
        $receipt->update(['status' => ReceiptStatus::Confirmed]);

        return $c;
    }
}
