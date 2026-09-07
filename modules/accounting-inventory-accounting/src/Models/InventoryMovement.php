<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\InventoryAccounting\Enums\MovementType;

final class InventoryMovement extends Model
{
    protected $table = 'accounting_inventory_movements';

    protected $fillable = ['item_id', 'movement_ref', 'movement_type', 'quantity', 'unit_cost', 'total_cost', 'source_ref', 'occurred_at', 'metadata'];

    protected $casts = ['movement_type' => MovementType::class, 'quantity' => 'decimal:4', 'unit_cost' => 'decimal:4', 'total_cost' => 'decimal:2', 'occurred_at' => 'datetime', 'metadata' => 'array'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}
