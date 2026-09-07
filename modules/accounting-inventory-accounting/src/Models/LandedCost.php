<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LandedCost extends Model
{
    protected $table = 'accounting_inventory_landed_costs';

    protected $fillable = ['item_id', 'cost_ref', 'amount', 'allocation_basis', 'source_ref', 'allocated_at', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'allocated_at' => 'datetime', 'metadata' => 'array'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}
