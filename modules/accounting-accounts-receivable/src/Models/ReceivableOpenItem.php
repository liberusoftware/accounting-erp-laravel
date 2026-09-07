<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\AccountsReceivable\Enums\ReceivableStatus;
use Liberu\Accounting\FinancialMasterData\Models\Party;

class ReceivableOpenItem extends Model
{
    protected $table = 'accounting_ar_open_items';

    protected $fillable = ['party_id', 'source_type', 'source_id', 'reference', 'issued_on', 'due_on', 'original_amount', 'applied_amount', 'currency', 'status', 'metadata'];

    protected $casts = ['issued_on' => 'date', 'due_on' => 'date', 'original_amount' => 'decimal:2', 'applied_amount' => 'decimal:2', 'status' => ReceivableStatus::class, 'metadata' => 'array'];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(ReceiptApplication::class, 'open_item_id');
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(ReceivableDispute::class, 'open_item_id');
    }

    public function outstanding(): float
    {
        return max(0, (float) $this->original_amount - (float) $this->applied_amount);
    }
}
