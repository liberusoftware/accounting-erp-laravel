<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\InventoryAccounting\Enums\MovementType;
use Liberu\Accounting\InventoryAccounting\Exceptions\InvalidInventory;
use Liberu\Accounting\InventoryAccounting\Models\InventoryItem;
use Liberu\Accounting\InventoryAccounting\Models\InventoryMovement;
use Liberu\Accounting\InventoryAccounting\Models\InventoryWriteDown;

final class WriteDownInventory
{
    public function handle(InventoryItem $item, array $attributes): InventoryWriteDown
    {
        $amount = (float) ($attributes['amount'] ?? 0);
        if ($amount <= 0 || blank($attributes['write_down_ref'] ?? null) || blank($attributes['reason'] ?? null) || $amount > (float) $item->inventory_value) {
            throw new InvalidInventory('Write-down must be positive, justified and no greater than inventory value.');
        }

        return DB::transaction(function () use ($item, $attributes, $amount): InventoryWriteDown {
            $write = InventoryWriteDown::create(['item_id' => $item->getKey(), 'write_down_ref' => $attributes['write_down_ref'], 'amount' => $amount, 'reason' => $attributes['reason'], 'actor_ref' => $attributes['actor_ref'] ?? null, 'written_down_at' => now()]);
            InventoryMovement::create(['item_id' => $item->getKey(), 'movement_ref' => $attributes['write_down_ref'], 'movement_type' => MovementType::WriteDown, 'quantity' => 0, 'unit_cost' => 0, 'total_cost' => $amount, 'source_ref' => $attributes['write_down_ref'], 'occurred_at' => now()]);
            $item->decrement('inventory_value', $amount);

            return $write;
        });
    }
}
