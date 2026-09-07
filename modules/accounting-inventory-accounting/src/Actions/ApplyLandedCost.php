<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\InventoryAccounting\Enums\MovementType;
use Liberu\Accounting\InventoryAccounting\Exceptions\InvalidInventory;
use Liberu\Accounting\InventoryAccounting\Models\InventoryItem;
use Liberu\Accounting\InventoryAccounting\Models\InventoryMovement;
use Liberu\Accounting\InventoryAccounting\Models\LandedCost;

final class ApplyLandedCost
{
    public function handle(InventoryItem $item, array $attributes): LandedCost
    {
        $amount = (float) ($attributes['amount'] ?? 0);
        if ($amount <= 0 || blank($attributes['cost_ref'] ?? null) || blank($attributes['allocation_basis'] ?? null) || blank($attributes['source_ref'] ?? null)) {
            throw new InvalidInventory('Landed cost requires positive amount, reference, allocation basis and source.');
        }

        return DB::transaction(function () use ($item, $attributes, $amount): LandedCost {
            $cost = LandedCost::create(['item_id' => $item->getKey(), 'cost_ref' => $attributes['cost_ref'], 'amount' => $amount, 'allocation_basis' => $attributes['allocation_basis'], 'source_ref' => $attributes['source_ref'], 'allocated_at' => now()]);
            InventoryMovement::create(['item_id' => $item->getKey(), 'movement_ref' => $attributes['cost_ref'], 'movement_type' => MovementType::LandedCost, 'quantity' => 0, 'unit_cost' => 0, 'total_cost' => $amount, 'source_ref' => $attributes['source_ref'], 'occurred_at' => now()]);
            $item->increment('inventory_value', $amount);

            return $cost;
        });
    }
}
