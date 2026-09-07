<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrders\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PurchaseOrders\Enums\PurchaseOrderStatus;
use Liberu\Accounting\PurchaseOrders\Exceptions\InvalidPurchaseOrder;
use Liberu\Accounting\PurchaseOrders\Models\PurchaseOrder;
use Liberu\Accounting\PurchaseOrders\Models\PurchaseOrderChange;

final class RecordPurchaseOrderChange
{
    public function handle(PurchaseOrder $order, array $attributes): PurchaseOrderChange
    {
        if (in_array($order->status, [PurchaseOrderStatus::Closed, PurchaseOrderStatus::Cancelled], true) || blank($attributes['reason'] ?? null) || ! is_array($attributes['changes'] ?? null)) {
            throw new InvalidPurchaseOrder('Closed orders cannot receive changes and changes require a reason.');
        }

        return DB::transaction(fn (): PurchaseOrderChange => PurchaseOrderChange::create(['order_id' => $order->id, 'version' => ((int) $order->changes()->max('version')) + 1, 'changes' => $attributes['changes'], 'reason' => $attributes['reason'], 'actor_ref' => $attributes['actor_ref'] ?? null, 'approved_at' => $attributes['approved_at'] ?? null, 'metadata' => $attributes['metadata'] ?? null]));
    }
}
