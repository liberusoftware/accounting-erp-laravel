<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrders\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SalesOrders\Enums\OrderStatus;
use Liberu\Accounting\SalesOrders\Exceptions\InvalidSalesOrder;
use Liberu\Accounting\SalesOrders\Models\SalesOrder;

final class CreateSalesOrder
{
    public function handle(array $attributes, array $lines): SalesOrder
    {
        return DB::transaction(function () use ($attributes, $lines): SalesOrder {
            foreach (['customer_id', 'currency', 'order_date'] as $key) {
                if (blank($attributes[$key] ?? null)) {
                    throw new InvalidSalesOrder("Sales order field [{$key}] is required.");
                }
            }if ($lines === []) {
                throw new InvalidSalesOrder('A sales order requires at least one line.');
            }$number = $attributes['order_number'] ?? 'SO-'.str_pad((string) (SalesOrder::query()->max('id') + 1), 6, '0', STR_PAD_LEFT);
            $order = SalesOrder::create(array_merge($attributes, ['order_number' => $number, 'status' => $attributes['status'] ?? OrderStatus::Draft]));
            foreach ($lines as $line) {
                $quantity = (float) ($line['quantity'] ?? 0);
                $price = (float) ($line['unit_price'] ?? 0);
                if ($quantity <= 0 || $price < 0) {
                    throw new InvalidSalesOrder('Order quantities must be positive and prices non-negative.');
                }$amount = round($quantity * $price, 2);
                $tax = round($amount * (float) ($line['tax_rate'] ?? 0) / 100, 2);
                $order->items()->create(array_merge($line, ['quantity' => $quantity, 'unit_price' => $price, 'amount' => $amount, 'tax_amount' => $tax]));
            }$order->update(['subtotal' => $order->items()->sum('amount'), 'tax_total' => $order->items()->sum('tax_amount'), 'total' => $order->items()->sum('amount') + $order->items()->sum('tax_amount')]);

            return $order->refresh()->load('items');
        });
    }
}
