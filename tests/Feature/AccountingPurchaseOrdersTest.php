<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\PurchaseOrders\Actions\CreatePurchaseOrder;
use Liberu\Accounting\PurchaseOrders\Actions\RecordPurchaseOrderChange;
use Liberu\Accounting\PurchaseOrders\Actions\RecordPurchaseReceipt;
use Liberu\Accounting\PurchaseOrders\Actions\TransitionPurchaseOrder;
use Liberu\Accounting\PurchaseOrders\Enums\PurchaseOrderStatus;
use Liberu\Accounting\PurchaseOrders\Exceptions\InvalidPurchaseOrder;

uses(RefreshDatabase::class);
it('issues an order, records partial and final receipts, changes, and closes it', function (): void {
    $order = app(CreatePurchaseOrder::class)->handle(['supplier_ref' => 'supplier-1', 'currency' => 'USD'], [['item_ref' => 'item-1', 'quantity' => 10, 'unit_price' => 5]]);
    app(TransitionPurchaseOrder::class)->handle($order, PurchaseOrderStatus::PendingApproval);
    app(TransitionPurchaseOrder::class)->handle($order->refresh(), PurchaseOrderStatus::Approved);
    app(TransitionPurchaseOrder::class)->handle($order->refresh(), PurchaseOrderStatus::Issued, ['commitment_ref' => 'commit-1']);
    app(RecordPurchaseReceipt::class)->handle($order->refresh(), ['lines' => [['line_id' => $order->lines->first()->id, 'quantity' => 4]]]);
    expect($order->refresh()->status)->toBe(PurchaseOrderStatus::PartiallyReceived);
    app(RecordPurchaseOrderChange::class)->handle($order->refresh(), ['changes' => ['expected_delivery_on' => '2026-09-01'], 'reason' => 'Supplier confirmed date']);
    app(RecordPurchaseReceipt::class)->handle($order->refresh(), ['lines' => [['line_id' => $order->lines->first()->id, 'quantity' => 6]]]);
    app(TransitionPurchaseOrder::class)->handle($order->refresh(), PurchaseOrderStatus::Closed);
    expect($order->refresh()->status)->toBe(PurchaseOrderStatus::Closed)->and($order->receipts)->toHaveCount(2)->and($order->changes)->toHaveCount(1);
});
it('rejects over-receipts and illegal changes after closure', function (): void {
    $order = app(CreatePurchaseOrder::class)->handle(['supplier_ref' => 'supplier-1', 'currency' => 'USD'], [['item_ref' => 'item-1', 'quantity' => 1, 'unit_price' => 5]]);
    app(TransitionPurchaseOrder::class)->handle($order, PurchaseOrderStatus::PendingApproval);
    app(TransitionPurchaseOrder::class)->handle($order->refresh(), PurchaseOrderStatus::Approved);
    app(TransitionPurchaseOrder::class)->handle($order->refresh(), PurchaseOrderStatus::Issued);
    expect(fn () => app(RecordPurchaseReceipt::class)->handle($order->refresh(), ['lines' => [['line_id' => $order->lines->first()->id, 'quantity' => 2]]]))->toThrow(InvalidPurchaseOrder::class);
});
