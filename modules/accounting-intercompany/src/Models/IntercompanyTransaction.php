<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\Intercompany\Enums\TransactionStatus;

/**
 * @property TransactionStatus $status
 * @property string $amount
 * @property string $currency
 */
final class IntercompanyTransaction extends Model
{
    protected $table = 'accounting_intercompany_transactions';

    protected $fillable = ['team_id', 'transaction_ref', 'counterparty_id', 'source_entity_ref', 'target_entity_ref', 'transaction_type', 'description', 'amount', 'currency', 'status', 'transaction_date', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'status' => TransactionStatus::class, 'transaction_date' => 'datetime', 'metadata' => 'array'];

    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(IntercompanyCounterparty::class, 'counterparty_id');
    }

    public function confirmations(): HasMany
    {
        return $this->hasMany(IntercompanyConfirmation::class, 'transaction_id');
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(IntercompanySettlement::class, 'transaction_id');
    }

    public function differences(): HasMany
    {
        return $this->hasMany(IntercompanyDifference::class, 'transaction_id');
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(TransferPricingEvidence::class, 'transaction_id');
    }
}
