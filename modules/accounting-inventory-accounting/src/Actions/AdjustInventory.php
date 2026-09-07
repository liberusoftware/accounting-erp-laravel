<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\InventoryAccounting\Exceptions\InvalidInventory;
use Liberu\Accounting\InventoryAccounting\Models\InventoryAdjustment;
use Liberu\Accounting\InventoryAccounting\Models\InventoryItem;

final class AdjustInventory
{
    public function handle(InventoryItem $item, array $attributes): InventoryAdjustment
    {
        $quantity = (float) ($attributes['quantity_delta'] ?? 0);
        $value = (float) ($attributes['value_delta'] ?? 0);
        if (blank($attributes['adjustment_ref'] ?? null) || blank($attributes['reason'] ?? null) || ($quantity == 0.0 && $value == 0.0)) {
            throw new InvalidInventory('Adjustment requires a reference, reason and non-zero delta.');
        }
        if ((float) $item->quantity_on_hand + $quantity < 0) {
            throw new InvalidInventory('Adjustment cannot make stock negative.');
        }

        return DB::transaction(function () use ($item, $attributes, $quantity, $value): InventoryAdjustment {
            $adjustment = InventoryAdjustment::create(['item_id' => $item->getKey(), 'adjustment_ref' => $attributes['adjustment_ref'], 'quantity_delta' => $quantity, 'value_delta' => $value, 'reason' => $attributes['reason'], 'actor_ref' => $attributes['actor_ref'] ?? null, 'adjusted_at' => now()]);
            $item->increment('quantity_on_hand', $quantity);
            $item->increment('inventory_value', $value);

            return $adjustment;
        });
    }
}
