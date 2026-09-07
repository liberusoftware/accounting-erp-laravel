<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class InventoryAdjustment extends Model
{
    protected $table = 'accounting_inventory_adjustments';

    protected $fillable = ['item_id', 'adjustment_ref', 'quantity_delta', 'value_delta', 'reason', 'actor_ref', 'adjusted_at', 'metadata'];

    protected $casts = ['quantity_delta' => 'decimal:4', 'value_delta' => 'decimal:2', 'adjusted_at' => 'datetime', 'metadata' => 'array'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}
