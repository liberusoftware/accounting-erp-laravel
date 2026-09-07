<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SalesOrderDeposit extends Model
{
    protected $table = 'accounting_sales_order_deposits';

    protected $fillable = ['sales_order_id', 'reference', 'amount', 'currency', 'status', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'metadata' => 'array'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }
}
