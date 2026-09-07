<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrders\Actions;

use Liberu\Accounting\SalesOrders\Exceptions\InvalidSalesOrder;
use Liberu\Accounting\SalesOrders\Models\SalesOrder;
use Liberu\Accounting\SalesOrders\Models\SalesOrderAllocation;

final class AddFulfillmentReference
{
    public function handle(SalesOrder $order, array $attributes): SalesOrderAllocation
    {
        foreach (['fulfillment_type', 'fulfillment_id', 'quantity'] as $key) {
            if (blank($attributes[$key] ?? null)) {
                throw new InvalidSalesOrder("Fulfillment field [{$key}] is required.");
            }
        }if ((float) $attributes['quantity'] <= 0) {
            throw new InvalidSalesOrder('Fulfillment quantity must be positive.');
        }

        return SalesOrderAllocation::query()->create(array_merge($attributes, ['sales_order_id' => $order->id]));
    }
}
