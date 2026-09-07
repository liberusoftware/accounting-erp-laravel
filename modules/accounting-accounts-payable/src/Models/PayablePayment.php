<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\AccountsPayable\Enums\PayableStatus;
use Liberu\Accounting\FinancialMasterData\Models\Party;

class PayablePayment extends Model
{
    protected $table = 'accounting_ap_payments';

    protected $fillable = ['party_id', 'paid_on', 'amount', 'applied_amount', 'currency', 'reference', 'status', 'metadata'];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    protected $casts = ['paid_on' => 'date', 'amount' => 'decimal:2', 'applied_amount' => 'decimal:2', 'status' => PayableStatus::class, 'metadata' => 'array'];

    public function applications(): HasMany
    {
        return $this->hasMany(PaymentApplication::class, 'payment_id');
    }

    public function unapplied(): float
    {
        return max(0, (float) $this->amount - (float) $this->applied_amount);
    }
}
