<?php

declare(strict_types=1);
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\SalesOrders\Actions\AddFulfillmentReference;
use Liberu\Accounting\SalesOrders\Actions\AddSalesOrderDeposit;
use Liberu\Accounting\SalesOrders\Actions\CreateSalesOrder;
use Liberu\Accounting\SalesOrders\Actions\RecordPartialInvoice;
use Liberu\Accounting\SalesOrders\Actions\TransitionSalesOrder;
use Liberu\Accounting\SalesOrders\Enums\OrderStatus;
use Liberu\Accounting\SalesOrders\Exceptions\InvalidSalesOrder;
use Liberu\Accounting\SalesOrders\Models\SalesOrder;

uses(RefreshDatabase::class);
function salesOrder(): SalesOrder
{
    return app(CreateSalesOrder::class)->handle(['customer_id' => 'customer-1', 'currency' => 'USD', 'order_date' => '2026-08-25'], [['description' => 'Service', 'quantity' => 2, 'unit_price' => 100, 'tax_rate' => 10]]);
}
it('preserves accepted demand, deposits, fulfillment, and partial invoicing', function (): void {
    $order = salesOrder();
    app(TransitionSalesOrder::class)->handle($order, OrderStatus::Confirmed);
    app(AddSalesOrderDeposit::class)->handle($order->refresh(), ['reference' => 'DEP-1', 'amount' => 50, 'currency' => 'USD']);
    app(AddFulfillmentReference::class)->handle($order->refresh(), ['fulfillment_type' => 'shipment', 'fulfillment_id' => 'SHIP-1', 'quantity' => 1]);
    $partial = app(RecordPartialInvoice::class)->handle($order->refresh(), 100, 'INV-1');
    expect($partial->status)->toBe(OrderStatus::PartiallyInvoiced)->and($partial->items)->toHaveCount(1)->and($partial->deposits)->toHaveCount(1)->and($partial->allocations)->toHaveCount(1);
});
it('closes after full invoicing and rejects invalid duplicate financial states', function (): void {
    $order = salesOrder();
    app(TransitionSalesOrder::class)->handle($order, OrderStatus::Confirmed);
    app(AddSalesOrderDeposit::class)->handle($order->refresh(), ['reference' => 'DEP-1', 'amount' => 1, 'currency' => 'USD']);
    app(RecordPartialInvoice::class)->handle($order->refresh(), 220, 'INV-1');
    expect($order->refresh()->status)->toBe(OrderStatus::Invoiced);
    expect(fn () => app(RecordPartialInvoice::class)->handle($order->refresh(), 1, 'INV-2'))->toThrow(InvalidSalesOrder::class);
    expect(fn () => app(AddSalesOrderDeposit::class)->handle($order->refresh(), ['reference' => 'DEP-1', 'amount' => 1, 'currency' => 'USD']))->toThrow(UniqueConstraintViolationException::class);
});
