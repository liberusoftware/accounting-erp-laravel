<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\FinancialMasterData\Models\Party;

class ReceivableAccount extends Model
{
    protected $table = 'accounting_ar_accounts';

    protected $fillable = ['party_id', 'credit_limit', 'current_balance', 'credit_hold', 'hold_reason', 'control_account_code', 'status', 'metadata'];

    protected $casts = ['credit_limit' => 'decimal:2', 'current_balance' => 'decimal:2', 'credit_hold' => 'boolean', 'metadata' => 'array'];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function openItems(): HasMany
    {
        return $this->hasMany(ReceivableOpenItem::class, 'party_id', 'party_id');
    }

    public function isOverLimit(): bool
    {
        return (float) $this->credit_limit > 0 && (float) $this->current_balance >= (float) $this->credit_limit;
    }
}
