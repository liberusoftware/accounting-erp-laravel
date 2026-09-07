<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\GoodsAndServiceReceipts\Enums\ReceiptStatus;
use Liberu\Accounting\GoodsAndServiceReceipts\Enums\ReceiptType;

/**
 * @property ReceiptStatus $status
 * @property ReceiptType $receipt_type
 * @property string $total_value
 * @property string|null $inventory_ref
 * @property string|null $project_ref
 * @property string $currency
 */
final class Receipt extends Model
{
    protected $table = 'accounting_goods_service_receipts';

    protected $fillable = ['team_id', 'receipt_ref', 'receipt_type', 'supplier_ref', 'purchase_order_ref', 'currency', 'status', 'received_at', 'inventory_ref', 'project_ref', 'total_value', 'metadata'];

    protected $casts = ['receipt_type' => ReceiptType::class, 'status' => ReceiptStatus::class, 'received_at' => 'datetime', 'total_value' => 'decimal:2', 'metadata' => 'array'];

    /** @return HasMany<ReceiptLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(ReceiptLine::class, 'receipt_id');
    }

    public function confirmations(): HasMany
    {
        return $this->hasMany(ServiceConfirmation::class, 'receipt_id');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ReceiptReturn::class, 'receipt_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ReceiptAttachment::class, 'receipt_id');
    }

    public function accruals(): HasMany
    {
        return $this->hasMany(ReceiptAccrual::class, 'receipt_id');
    }
}
