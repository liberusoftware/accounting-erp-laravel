<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $quantity_remaining
 * @property string $unit_cost
 * @property string $total_cost
 */
final class CostLayer extends Model
{
    protected $table = 'accounting_inventory_cost_layers';

    protected $fillable = ['item_id', 'layer_ref', 'received_at', 'quantity_received', 'quantity_remaining', 'unit_cost', 'total_cost', 'source_ref', 'metadata'];

    protected $casts = ['received_at' => 'datetime', 'quantity_received' => 'decimal:4', 'quantity_remaining' => 'decimal:4', 'unit_cost' => 'decimal:4', 'total_cost' => 'decimal:2', 'metadata' => 'array'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}
