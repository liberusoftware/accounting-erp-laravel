<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\AccountsPayable\Enums\PayableStatus;
use Liberu\Accounting\FinancialMasterData\Models\Party;

class PayableOpenItem extends Model
{
    protected $table = 'accounting_ap_open_items';

    protected $fillable = ['party_id', 'source_type', 'source_id', 'reference', 'issued_on', 'due_on', 'original_amount', 'paid_amount', 'currency', 'payment_terms', 'status', 'metadata'];

    protected $casts = ['issued_on' => 'date', 'due_on' => 'date', 'original_amount' => 'decimal:2', 'paid_amount' => 'decimal:2', 'status' => PayableStatus::class, 'metadata' => 'array'];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(PaymentApplication::class, 'open_item_id');
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(PayableDispute::class, 'open_item_id');
    }

    public function outstanding(): float
    {
        return max(0, (float) $this->original_amount - (float) $this->paid_amount);
    }
}
