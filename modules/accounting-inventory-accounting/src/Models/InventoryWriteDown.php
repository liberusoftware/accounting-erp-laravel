<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class InventoryWriteDown extends Model
{
    protected $table = 'accounting_inventory_write_downs';

    protected $fillable = ['item_id', 'write_down_ref', 'amount', 'reason', 'actor_ref', 'written_down_at', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'written_down_at' => 'datetime', 'metadata' => 'array'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}
