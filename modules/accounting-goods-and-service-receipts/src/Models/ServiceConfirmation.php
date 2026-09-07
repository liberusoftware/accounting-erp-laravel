<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ServiceConfirmation extends Model
{
    protected $table = 'accounting_goods_service_confirmations';

    protected $fillable = ['receipt_id', 'confirmation_ref', 'service_period', 'quantity', 'value', 'confirmed_by', 'confirmed_at', 'comment', 'metadata'];

    protected $casts = ['quantity' => 'decimal:4', 'value' => 'decimal:2', 'confirmed_at' => 'datetime', 'metadata' => 'array'];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }
}
