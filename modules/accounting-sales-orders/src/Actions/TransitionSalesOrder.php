<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrders\Actions;

use Liberu\Accounting\SalesOrders\Enums\OrderStatus;
use Liberu\Accounting\SalesOrders\Exceptions\InvalidSalesOrder;
use Liberu\Accounting\SalesOrders\Models\SalesOrder;

final class TransitionSalesOrder
{
    public function handle(SalesOrder $order, OrderStatus|string $status): SalesOrder
    {
        $next = $status instanceof OrderStatus ? $status : OrderStatus::tryFrom($status);
        if (! $next) {
            throw new InvalidSalesOrder('Unknown sales order status.');
        }$allowed = [OrderStatus::Draft->value => [OrderStatus::Confirmed, OrderStatus::Cancelled], OrderStatus::Confirmed->value => [OrderStatus::PartiallyInvoiced, OrderStatus::Invoiced, OrderStatus::Cancelled], OrderStatus::PartiallyInvoiced->value => [OrderStatus::Invoiced, OrderStatus::Cancelled], OrderStatus::Invoiced->value => [], OrderStatus::Cancelled->value => []];
        if (! in_array($next, $allowed[$order->status->value], true)) {
            throw new InvalidSalesOrder("Cannot transition {$order->status->value} to {$next->value}.");
        }$order->update(['status' => $next]);

        return $order->refresh();
    }
}
