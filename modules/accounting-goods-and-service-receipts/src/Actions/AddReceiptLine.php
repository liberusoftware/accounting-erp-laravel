<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\GoodsAndServiceReceipts\Enums\ReceiptStatus;
use Liberu\Accounting\GoodsAndServiceReceipts\Exceptions\InvalidReceipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\Receipt;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\ReceiptLine;

final class AddReceiptLine
{
    public function handle(Receipt $receipt, array $a): ReceiptLine
    {
        $received = (float) ($a['received_quantity'] ?? 0);
        $ordered = isset($a['ordered_quantity']) ? (float) $a['ordered_quantity'] : null;
        $price = (float) ($a['unit_price'] ?? -1);
        foreach (['line_ref', 'description'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidReceipt("Missing line field [{$k}].");
            }
        }
        if ($received <= 0 || $price < 0 || ($ordered !== null && $ordered < 0)) {
            throw new InvalidReceipt('Receipt quantities and price are invalid.');
        }
        if ($receipt->status !== ReceiptStatus::Draft) {
            throw new InvalidReceipt('Lines can only be added to draft receipts.');
        }

        return DB::transaction(function () use ($receipt, $a, $received, $ordered, $price): ReceiptLine {
            $variance = $ordered === null ? 0 : $received - $ordered;
            $line = ReceiptLine::create(['receipt_id' => $receipt->getKey(), 'line_ref' => $a['line_ref'], 'item_ref' => $a['item_ref'] ?? null, 'description' => $a['description'], 'ordered_quantity' => $ordered, 'received_quantity' => $received, 'unit_price' => $price, 'line_value' => round($received * $price, 2), 'variance_quantity' => $variance, 'variance_value' => round($variance * $price, 2), 'inventory_ref' => $a['inventory_ref'] ?? $receipt->inventory_ref, 'project_ref' => $a['project_ref'] ?? $receipt->project_ref, 'metadata' => $a['metadata'] ?? null]);
            $receipt->increment('total_value', (float) $line->line_value);

            return $line;
        });
    }
}
