<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrders\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Liberu\Accounting\PurchaseOrders\Enums\PurchaseOrderStatus;
use Liberu\Accounting\PurchaseOrders\Exceptions\InvalidPurchaseOrder;
use Liberu\Accounting\PurchaseOrders\Models\PurchaseOrder;
use Liberu\Accounting\PurchaseOrders\Models\PurchaseOrderLine;
use Liberu\Accounting\PurchaseOrders\Models\PurchaseReceipt;

final class RecordPurchaseReceipt
{
    public function handle(PurchaseOrder $order, array $attributes): PurchaseReceipt
    {
        return DB::transaction(function () use ($order, $attributes): PurchaseReceipt {
            $lines = $attributes['lines'] ?? [];
            if ($order->status !== PurchaseOrderStatus::Issued && $order->status !== PurchaseOrderStatus::PartiallyReceived) {
                throw new InvalidPurchaseOrder('Only issued orders can receive goods.');
            }foreach ($lines as $line) {
                $item = PurchaseOrderLine::query()->where('order_id', $order->id)->lockForUpdate()->find($line['line_id'] ?? 0);
                $qty = (float) ($line['quantity'] ?? 0);
                if ($item === null || $qty <= 0 || $qty > (float) $item->quantity - (float) $item->received_quantity) {
                    throw new InvalidPurchaseOrder('Receipt exceeds remaining ordered quantity.');
                }$item->update(['received_quantity' => (float) $item->received_quantity + $qty]);
            }$receipt = PurchaseReceipt::create(['order_id' => $order->id, 'receipt_ref' => $attributes['receipt_ref'] ?? ('GRN-'.$order->id.'-'.Str::uuid()->toString()), 'received_on' => $attributes['received_on'] ?? now()->toDateString(), 'lines' => $lines, 'status' => 'accepted', 'document_ref' => $attributes['document_ref'] ?? null]);
            $remaining = $order->lines()->whereColumn('received_quantity', '<', 'quantity')->exists();
            $order->update(['status' => $remaining ? PurchaseOrderStatus::PartiallyReceived : PurchaseOrderStatus::Received]);

            return $receipt;
        });
    }
}
