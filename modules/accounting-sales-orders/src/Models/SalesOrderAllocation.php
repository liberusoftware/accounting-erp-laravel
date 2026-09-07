<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SalesOrderAllocation extends Model
{
    protected $table = 'accounting_sales_order_allocations';

    protected $fillable = ['sales_order_id', 'item_id', 'fulfillment_type', 'fulfillment_id', 'quantity', 'status', 'metadata'];

    protected $casts = ['quantity' => 'decimal:4', 'metadata' => 'array'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }
}
