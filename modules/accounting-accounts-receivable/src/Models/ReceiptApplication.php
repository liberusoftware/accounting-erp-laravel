<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptApplication extends Model
{
    protected $table = 'accounting_ar_receipt_applications';

    protected $fillable = ['receipt_id', 'open_item_id', 'amount'];

    protected $casts = ['amount' => 'decimal:2'];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(ReceivableReceipt::class, 'receipt_id');
    }

    public function openItem(): BelongsTo
    {
        return $this->belongsTo(ReceivableOpenItem::class, 'open_item_id');
    }
}
