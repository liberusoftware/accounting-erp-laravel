<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrders\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property string $item_ref
 * @property float|string $quantity
 * @property float|string $unit_price
 * @property float|string $received_quantity
 */
final class PurchaseOrderLine extends Model
{
    protected $table = 'accounting_purchase_order_lines';

    protected $fillable = ['order_id', 'item_ref', 'description', 'quantity', 'unit_price', 'received_quantity', 'delivery_metadata', 'metadata'];

    protected $casts = ['quantity' => 'decimal:4', 'unit_price' => 'decimal:2', 'received_quantity' => 'decimal:4', 'delivery_metadata' => 'array', 'metadata' => 'array'];
}
