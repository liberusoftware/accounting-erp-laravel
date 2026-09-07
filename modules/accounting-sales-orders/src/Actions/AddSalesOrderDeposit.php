<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrders\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SalesOrders\Exceptions\InvalidSalesOrder;
use Liberu\Accounting\SalesOrders\Models\SalesOrder;
use Liberu\Accounting\SalesOrders\Models\SalesOrderDeposit;

final class AddSalesOrderDeposit
{
    public function handle(SalesOrder $order, array $attributes): SalesOrderDeposit
    {
        return DB::transaction(function () use ($order, $attributes): SalesOrderDeposit {
            $amount = (float) ($attributes['amount'] ?? 0);
            if ($amount <= 0 || blank($attributes['reference'] ?? null)) {
                throw new InvalidSalesOrder('A deposit requires a positive amount and reference.');
            }if ((float) $order->deposits()->sum('amount') + $amount > (float) $order->total) {
                throw new InvalidSalesOrder('Deposits cannot exceed the order total.');
            }

            return SalesOrderDeposit::query()->create(array_merge($attributes, ['sales_order_id' => $order->id]));
        });
    }
}
