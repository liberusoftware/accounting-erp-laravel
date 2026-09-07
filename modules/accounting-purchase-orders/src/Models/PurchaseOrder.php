<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\PurchaseOrders\Enums\PurchaseOrderStatus;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string $supplier_ref
 * @property string $order_number
 * @property PurchaseOrderStatus $status
 * @property float|string $total_amount
 * @property string|null $expected_delivery_on
 * @property string|null $commitment_ref
 */
final class PurchaseOrder extends Model
{
    protected $table = 'accounting_purchase_orders_module';

    protected $fillable = ['team_id', 'supplier_ref', 'order_number', 'currency', 'order_date', 'expected_delivery_on', 'total_amount', 'status', 'commitment_ref', 'source_requisition_ref', 'notes', 'metadata'];

    protected $casts = ['status' => PurchaseOrderStatus::class, 'order_date' => 'date', 'expected_delivery_on' => 'date', 'total_amount' => 'decimal:2', 'metadata' => 'array'];

    /** @return HasMany<PurchaseOrderLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseOrderLine::class, 'order_id');
    }

    /** @return HasMany<PurchaseReceipt, $this> */
    public function receipts(): HasMany
    {
        return $this->hasMany(PurchaseReceipt::class, 'order_id');
    }

    /** @return HasMany<PurchaseOrderChange, $this> */
    public function changes(): HasMany
    {
        return $this->hasMany(PurchaseOrderChange::class, 'order_id');
    }
}
