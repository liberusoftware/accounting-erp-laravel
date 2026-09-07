<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReceiptAccrual extends Model
{
    protected $table = 'accounting_goods_service_receipt_accruals';

    protected $fillable = ['receipt_id', 'accrual_ref', 'amount', 'currency', 'period_ref', 'status', 'posted_at', 'source_ref', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'posted_at' => 'datetime', 'metadata' => 'array'];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }
}
