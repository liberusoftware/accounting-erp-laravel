<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\InventoryAccounting\Enums\MovementType;
use Liberu\Accounting\InventoryAccounting\Exceptions\InvalidInventory;
use Liberu\Accounting\InventoryAccounting\Models\CostLayer;
use Liberu\Accounting\InventoryAccounting\Models\InventoryItem;
use Liberu\Accounting\InventoryAccounting\Models\InventoryMovement;

final class ReceiveInventory
{
    public function handle(InventoryItem $item, array $attributes): CostLayer
    {
        $quantity = (float) ($attributes['quantity'] ?? 0);
        $unit = (float) ($attributes['unit_cost'] ?? -1);
        if ($quantity <= 0 || $unit < 0 || blank($attributes['movement_ref'] ?? null) || blank($attributes['source_ref'] ?? null)) {
            throw new InvalidInventory('Receipt requires positive quantity, non-negative cost, movement reference and source.');
        }

        return DB::transaction(function () use ($item, $attributes, $quantity, $unit): CostLayer {
            $layer = CostLayer::create(['item_id' => $item->getKey(), 'layer_ref' => $attributes['movement_ref'], 'received_at' => $attributes['occurred_at'] ?? now(), 'quantity_received' => $quantity, 'quantity_remaining' => $quantity, 'unit_cost' => $unit, 'total_cost' => round($quantity * $unit, 2), 'source_ref' => $attributes['source_ref']]);
            InventoryMovement::create(['item_id' => $item->getKey(), 'movement_ref' => $attributes['movement_ref'], 'movement_type' => MovementType::Receipt, 'quantity' => $quantity, 'unit_cost' => $unit, 'total_cost' => round($quantity * $unit, 2), 'source_ref' => $attributes['source_ref'], 'occurred_at' => $attributes['occurred_at'] ?? now()]);
            $item->increment('quantity_on_hand', $quantity);
            $item->increment('inventory_value', round($quantity * $unit, 2));

            return $layer;
        });
    }
}
