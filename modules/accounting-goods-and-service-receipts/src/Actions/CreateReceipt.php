<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Actions;

use Liberu\Accounting\GoodsAndServiceReceipts\Enums\ReceiptStatus;
use Liberu\Accounting\GoodsAndServiceReceipts\Enums\ReceiptType;
use Liberu\Accounting\GoodsAndServiceReceipts\Exceptions\InvalidReceipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\Receipt;

final class CreateReceipt
{
    public function handle(array $a): Receipt
    {
        $type = ReceiptType::tryFrom((string) ($a['receipt_type'] ?? ''));
        foreach (['receipt_ref', 'supplier_ref', 'currency'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidReceipt("Missing receipt field [{$k}].");
            }
        }if (! $type) {
            throw new InvalidReceipt('Receipt type must be goods or service.');
        }

        return Receipt::create(['team_id' => $a['team_id'] ?? null, 'receipt_ref' => $a['receipt_ref'], 'receipt_type' => $type, 'supplier_ref' => $a['supplier_ref'], 'purchase_order_ref' => $a['purchase_order_ref'] ?? null, 'currency' => strtoupper($a['currency']), 'status' => ReceiptStatus::Draft, 'received_at' => $a['received_at'] ?? now(), 'inventory_ref' => $a['inventory_ref'] ?? null, 'project_ref' => $a['project_ref'] ?? null, 'metadata' => $a['metadata'] ?? null]);
    }
}
