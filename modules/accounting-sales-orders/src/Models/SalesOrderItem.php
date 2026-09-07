<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $description
 * @property float|string $quantity
 * @property float|string $unit_price
 * @property float|string $amount
 * @property float|string $tax_amount
 * @property float|string $invoiced_quantity
 * @property-read SalesOrder $order
 */
final class SalesOrderItem extends Model
{
    protected $table = 'accounting_sales_order_items';

    protected $fillable = ['sales_order_id', 'sku', 'description', 'quantity', 'unit_price', 'amount', 'tax_rate', 'tax_amount', 'invoiced_quantity', 'metadata'];

    protected $casts = ['quantity' => 'decimal:4', 'unit_price' => 'decimal:4', 'amount' => 'decimal:2', 'tax_rate' => 'decimal:6', 'tax_amount' => 'decimal:2', 'invoiced_quantity' => 'decimal:4', 'metadata' => 'array'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }
}
