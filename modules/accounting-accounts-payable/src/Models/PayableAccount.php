<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\FinancialMasterData\Models\Party;

class PayableAccount extends Model
{
    protected $table = 'accounting_ap_accounts';

    protected $fillable = ['party_id', 'current_balance', 'payment_hold', 'hold_reason', 'control_account_code', 'status', 'metadata'];

    protected $casts = ['current_balance' => 'decimal:2', 'payment_hold' => 'boolean', 'metadata' => 'array'];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function openItems(): HasMany
    {
        return $this->hasMany(PayableOpenItem::class, 'party_id', 'party_id');
    }
}
