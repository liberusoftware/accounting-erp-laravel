<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Actions;

use Liberu\Accounting\GoodsAndServiceReceipts\Exceptions\InvalidReceipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\Receipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\ReceiptAttachment;

final class AddReceiptAttachment
{
    public function handle(Receipt $receipt, array $a): ReceiptAttachment
    {
        foreach (['attachment_ref', 'kind', 'file_ref', 'attached_by'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidReceipt("Missing attachment field [{$k}].");
            }
        }

        return ReceiptAttachment::create(['receipt_id' => $receipt->getKey(), 'attachment_ref' => $a['attachment_ref'], 'kind' => $a['kind'], 'file_ref' => $a['file_ref'], 'description' => $a['description'] ?? null, 'checksum' => $a['checksum'] ?? null, 'attached_by' => $a['attached_by'], 'attached_at' => now(), 'metadata' => $a['metadata'] ?? null]);
    }
}
