<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrders\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PurchaseOrders\Enums\PurchaseOrderStatus;
use Liberu\Accounting\PurchaseOrders\Exceptions\InvalidPurchaseOrder;
use Liberu\Accounting\PurchaseOrders\Models\PurchaseOrder;

final class TransitionPurchaseOrder
{
    public function handle(PurchaseOrder $order, PurchaseOrderStatus $status, array $attributes = []): PurchaseOrder
    {
        $valid = match ($order->status) {
            PurchaseOrderStatus::Draft => [PurchaseOrderStatus::PendingApproval, PurchaseOrderStatus::Cancelled],PurchaseOrderStatus::PendingApproval => [PurchaseOrderStatus::Approved, PurchaseOrderStatus::Cancelled],PurchaseOrderStatus::Approved => [PurchaseOrderStatus::Issued, PurchaseOrderStatus::Cancelled],PurchaseOrderStatus::Issued,PurchaseOrderStatus::PartiallyReceived => [PurchaseOrderStatus::Closed],PurchaseOrderStatus::Received => [PurchaseOrderStatus::Closed],default => []
        };
        if (! in_array($status, $valid, true)) {
            throw new InvalidPurchaseOrder("Cannot transition order from {$order->status->value} to {$status->value}.");
        }

        return DB::transaction(function () use ($order, $status, $attributes): PurchaseOrder {
            $order->update(['status' => $status, 'commitment_ref' => $attributes['commitment_ref'] ?? $order->commitment_ref]);

            return $order->refresh();
        });
    }
}
