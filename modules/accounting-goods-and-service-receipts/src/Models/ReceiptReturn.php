<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReceiptReturn extends Model
{
    protected $table = 'accounting_goods_service_receipt_returns';

    protected $fillable = ['receipt_id', 'return_ref', 'line_ref', 'quantity', 'value', 'reason', 'source_ref', 'returned_at', 'metadata'];

    protected $casts = ['quantity' => 'decimal:4', 'value' => 'decimal:2', 'returned_at' => 'datetime', 'metadata' => 'array'];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }
}
