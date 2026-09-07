<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Actions;

use Liberu\Accounting\InventoryAccounting\Enums\InventoryStatus;
use Liberu\Accounting\InventoryAccounting\Enums\ValuationMethod;
use Liberu\Accounting\InventoryAccounting\Exceptions\InvalidInventory;
use Liberu\Accounting\InventoryAccounting\Models\InventoryItem;

final class CreateInventoryItem
{
    public function handle(array $attributes): InventoryItem
    {
        $method = ValuationMethod::tryFrom((string) ($attributes['valuation_method'] ?? ''));
        foreach (['item_ref', 'description', 'warehouse_ref', 'currency'] as $key) {
            if (blank($attributes[$key] ?? null)) {
                throw new InvalidInventory("Missing item field [{$key}].");
            }
        }if (! $method) {
            throw new InvalidInventory('A supported valuation method is required.');
        }

        return InventoryItem::create(['team_id' => $attributes['team_id'] ?? null, 'item_ref' => $attributes['item_ref'], 'description' => $attributes['description'], 'warehouse_ref' => $attributes['warehouse_ref'], 'currency' => strtoupper($attributes['currency']), 'valuation_method' => $method, 'status' => InventoryStatus::Active, 'metadata' => $attributes['metadata'] ?? null]);
    }
}
