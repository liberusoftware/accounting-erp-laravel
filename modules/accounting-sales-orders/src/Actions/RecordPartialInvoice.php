<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrders\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SalesOrders\Enums\OrderStatus;
use Liberu\Accounting\SalesOrders\Exceptions\InvalidSalesOrder;
use Liberu\Accounting\SalesOrders\Models\SalesOrder;

final class RecordPartialInvoice
{
    public function handle(SalesOrder $order, float $amount, string $invoiceReference): SalesOrder
    {
        return DB::transaction(function () use ($order, $amount, $invoiceReference): SalesOrder {
            if ($amount <= 0 || blank($invoiceReference)) {
                throw new InvalidSalesOrder('An invoice requires a positive amount and reference.');
            }if (in_array($order->status, [OrderStatus::Draft, OrderStatus::Cancelled, OrderStatus::Invoiced], true) || $order->invoiced_total + $amount > $order->total) {
                throw new InvalidSalesOrder('This order cannot accept the requested invoice amount.');
            }$newTotal = (float) $order->invoiced_total + $amount;
            $order->update(['invoiced_total' => $newTotal, 'status' => $newTotal >= $order->total ? OrderStatus::Invoiced : OrderStatus::PartiallyInvoiced, 'metadata' => array_merge($order->metadata ?? [], ['invoice_references' => array_values(array_unique(array_merge($order->metadata['invoice_references'] ?? [], [$invoiceReference])))])]);

            return $order->refresh();
        });
    }
}
