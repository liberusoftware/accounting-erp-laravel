<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $line_ref
 * @property string|null $ordered_quantity
 * @property string $received_quantity
 * @property string $returned_quantity
 * @property string $line_value
 * @property string $variance_quantity
 * @property string $variance_value
 */
final class ReceiptLine extends Model
{
    protected $table = 'accounting_goods_service_receipt_lines';

    protected $fillable = ['receipt_id', 'line_ref', 'item_ref', 'description', 'ordered_quantity', 'received_quantity', 'returned_quantity', 'unit_price', 'line_value', 'variance_quantity', 'variance_value', 'inventory_ref', 'project_ref', 'metadata'];

    protected $casts = ['ordered_quantity' => 'decimal:4', 'received_quantity' => 'decimal:4', 'returned_quantity' => 'decimal:4', 'unit_price' => 'decimal:4', 'line_value' => 'decimal:2', 'variance_quantity' => 'decimal:4', 'variance_value' => 'decimal:2', 'metadata' => 'array'];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }
}
