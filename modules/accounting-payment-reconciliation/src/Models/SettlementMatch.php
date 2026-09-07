<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SettlementMatch extends Model
{
    protected $table = 'accounting_payment_reconciliation_matches';

    protected $fillable = ['run_id', 'item_id', 'reference_type', 'reference_id', 'matched_amount', 'confidence', 'status', 'matched_by', 'matched_at', 'idempotency_key', 'metadata'];

    protected $casts = ['matched_amount' => 'decimal:2', 'confidence' => 'decimal:4', 'matched_at' => 'datetime', 'metadata' => 'array'];

    public function run(): BelongsTo
    {
        return $this->belongsTo(SettlementRun::class, 'run_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(SettlementItem::class, 'item_id');
    }
}
