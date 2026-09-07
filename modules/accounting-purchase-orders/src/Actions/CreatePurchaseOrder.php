<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrders\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PurchaseOrders\Enums\PurchaseOrderStatus;
use Liberu\Accounting\PurchaseOrders\Exceptions\InvalidPurchaseOrder;
use Liberu\Accounting\PurchaseOrders\Models\PurchaseOrder;

final class CreatePurchaseOrder
{
    public function handle(array $attributes, array $lines): PurchaseOrder
    {
        $total = 0;
        foreach ($lines as $line) {
            if (blank($line['item_ref'] ?? null) || (float) ($line['quantity'] ?? 0) <= 0 || (float) ($line['unit_price'] ?? 0) < 0) {
                throw new InvalidPurchaseOrder('Each line requires item, positive quantity, and non-negative price.');
            }$total += round((float) $line['quantity'] * (float) $line['unit_price'], 2);
        }if (blank($attributes['supplier_ref'] ?? null) || blank($attributes['currency'] ?? null) || $total <= 0) {
            throw new InvalidPurchaseOrder('Supplier, currency, and positive order total are required.');
        }

        return DB::transaction(function () use ($attributes, $lines, $total): PurchaseOrder {
            $order = PurchaseOrder::create(['team_id' => $attributes['team_id'] ?? null, 'supplier_ref' => $attributes['supplier_ref'], 'order_number' => $attributes['order_number'] ?? ('PO-'.now()->format('YmdHis').'-'.str()->random(5)), 'currency' => strtoupper($attributes['currency']), 'order_date' => $attributes['order_date'] ?? now()->toDateString(), 'expected_delivery_on' => $attributes['expected_delivery_on'] ?? null, 'total_amount' => $total, 'status' => PurchaseOrderStatus::Draft, 'source_requisition_ref' => $attributes['source_requisition_ref'] ?? null, 'notes' => $attributes['notes'] ?? null, 'metadata' => $attributes['metadata'] ?? null]);
            foreach ($lines as $line) {
                $order->lines()->create(['item_ref' => $line['item_ref'], 'description' => $line['description'] ?? null, 'quantity' => $line['quantity'], 'unit_price' => $line['unit_price'], 'received_quantity' => 0, 'delivery_metadata' => $line['delivery_metadata'] ?? null]);
            }

            return $order->load('lines');
        });
    }
}
