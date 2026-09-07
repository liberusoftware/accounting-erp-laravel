<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\AccountsReceivable\Enums\ReceivableStatus;
use Liberu\Accounting\FinancialMasterData\Models\Party;

class ReceivableReceipt extends Model
{
    protected $table = 'accounting_ar_receipts';

    protected $fillable = ['party_id', 'received_on', 'amount', 'applied_amount', 'currency', 'reference', 'status', 'metadata'];

    protected $casts = ['received_on' => 'date', 'amount' => 'decimal:2', 'applied_amount' => 'decimal:2', 'status' => ReceivableStatus::class, 'metadata' => 'array'];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(ReceiptApplication::class, 'receipt_id');
    }

    public function unapplied(): float
    {
        return max(0, (float) $this->amount - (float) $this->applied_amount);
    }
}
