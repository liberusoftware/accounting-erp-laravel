<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\InventoryAccounting\Enums\MovementType;
use Liberu\Accounting\InventoryAccounting\Exceptions\InvalidInventory;
use Liberu\Accounting\InventoryAccounting\Models\InventoryItem;
use Liberu\Accounting\InventoryAccounting\Models\InventoryMovement;

final class IssueInventory
{
    public function handle(InventoryItem $item, array $attributes): float
    {
        $quantity = (float) ($attributes['quantity'] ?? 0);
        if ($quantity <= 0 || blank($attributes['movement_ref'] ?? null) || blank($attributes['source_ref'] ?? null)) {
            throw new InvalidInventory('Issue requires a positive quantity, movement reference and source.');
        }if ((float) $item->quantity_on_hand < $quantity) {
            throw new InvalidInventory('Insufficient quantity on hand.');
        }

        return DB::transaction(function () use ($item, $attributes, $quantity): float {
            $remaining = $quantity;
            $cost = 0.0;
            foreach ($item->layers()->where('quantity_remaining', '>', 0)->orderBy('received_at')->lockForUpdate()->get() as $layer) {
                $take = min($remaining, (float) $layer->quantity_remaining);
                $cost += round($take * (float) $layer->unit_cost, 2);
                $layer->decrement('quantity_remaining', $take);
                $remaining -= $take;
                if ($remaining <= 0) {
                    break;
                }
            }if ($remaining > 0) {
                throw new InvalidInventory('Cost layers cannot satisfy the issue.');
            }$unit = round($cost / $quantity, 4);
            InventoryMovement::create(['item_id' => $item->getKey(), 'movement_ref' => $attributes['movement_ref'], 'movement_type' => MovementType::Issue, 'quantity' => $quantity, 'unit_cost' => $unit, 'total_cost' => $cost, 'source_ref' => $attributes['source_ref'], 'occurred_at' => $attributes['occurred_at'] ?? now()]);
            $item->decrement('quantity_on_hand', $quantity);
            $item->decrement('inventory_value', $cost);

            return $cost;
        });
    }
}
