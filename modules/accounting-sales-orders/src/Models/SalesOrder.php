<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrders\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\SalesOrders\Enums\OrderStatus;

/**
 * @property int $id
 * @property string $customer_id
 * @property string|null $estimate_id
 * @property string $order_number
 * @property OrderStatus $status
 * @property float|string $subtotal
 * @property float|string $tax_total
 * @property float|string $total
 * @property float|string $invoiced_total
 * @property string $currency
 * @property-read Collection<int, SalesOrderItem> $items
 * @property-read Collection<int, SalesOrderDeposit> $deposits
 * @property-read Collection<int, SalesOrderAllocation> $allocations
 */
final class SalesOrder extends Model
{
    protected $table = 'accounting_sales_orders';

    protected $fillable = ['team_id', 'customer_id', 'estimate_id', 'order_number', 'order_date', 'status', 'currency', 'subtotal', 'tax_total', 'total', 'invoiced_total', 'notes', 'metadata'];

    protected $casts = ['status' => OrderStatus::class, 'order_date' => 'date', 'subtotal' => 'decimal:2', 'tax_total' => 'decimal:2', 'total' => 'decimal:2', 'invoiced_total' => 'decimal:2', 'metadata' => 'array'];

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class, 'sales_order_id');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(SalesOrderDeposit::class, 'sales_order_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(SalesOrderAllocation::class, 'sales_order_id');
    }
}
