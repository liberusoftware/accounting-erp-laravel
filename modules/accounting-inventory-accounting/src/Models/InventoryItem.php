<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\InventoryAccounting\Enums\InventoryStatus;
use Liberu\Accounting\InventoryAccounting\Enums\ValuationMethod;

/**
 * @property ValuationMethod $valuation_method
 * @property InventoryStatus $status
 * @property string $item_ref
 * @property string $quantity_on_hand
 * @property string $inventory_value
 */
final class InventoryItem extends Model
{
    protected $table = 'accounting_inventory_items';

    protected $fillable = ['team_id', 'item_ref', 'description', 'warehouse_ref', 'currency', 'valuation_method', 'status', 'quantity_on_hand', 'inventory_value', 'metadata'];

    protected $casts = ['valuation_method' => ValuationMethod::class, 'status' => InventoryStatus::class, 'quantity_on_hand' => 'decimal:4', 'inventory_value' => 'decimal:2', 'metadata' => 'array'];

    /** @return HasMany<CostLayer, $this> */
    public function layers(): HasMany
    {
        return $this->hasMany(CostLayer::class, 'item_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'item_id');
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(InventoryAdjustment::class, 'item_id');
    }

    public function writeDowns(): HasMany
    {
        return $this->hasMany(InventoryWriteDown::class, 'item_id');
    }

    public function landedCosts(): HasMany
    {
        return $this->hasMany(LandedCost::class, 'item_id');
    }
}
